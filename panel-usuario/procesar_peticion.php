<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// Lista blanca de roles permitidos
$rolesPermitidos = ['usuario'];
if (!in_array($_SESSION['usuario']['rol'], $rolesPermitidos)) {
    header('Location: ../login.php?error=acceso_no_autorizado');
    exit();
}

// Verificar que se recibieron los datos por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inventario.php?error=metodo_no_valido');
    exit();
}

// Conexión a BD
require_once '../conexion.php';

try {
    // Validar y obtener datos del formulario
    $cantidad = intval($_POST['cantidad']);
    $formacionodependencia = trim($_POST['formacionodependencia']);
    $cargopre = trim($_POST['cargopre']);
    $lugardetraslado = trim($_POST['lugardetraslado']);
    $idelementos = intval($_POST['idelementos']);
    $idpersonas = intval($_POST['idpersonas']);
    $iddetalle = isset($_POST['iddetalle']) ? intval($_POST['iddetalle']) : 0;

    // Validaciones básicas
    if (empty($formacionodependencia) || empty($cargopre) || empty($lugardetraslado) || $cantidad <= 0) {
        throw new Exception('Todos los campos obligatorios deben ser completados.');
    }

    // Verificar que el elemento exista y tenga stock suficiente
    $sql_verificar = "SELECT cantidadele, estadoelemento, estado, nombreele, descripcionele, codigoinventario FROM elementos WHERE IDele = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("i", $idelementos);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();
    
    if ($resultado->num_rows === 0) {
        throw new Exception('El elemento seleccionado no existe.');
    }
    
    $elemento = $resultado->fetch_assoc();
    
    if ($elemento['estadoelemento'] !== 'activo' || $elemento['estado'] === 'en prestamo') {
        throw new Exception('El elemento seleccionado no está disponible.');
    }
    
    if ($elemento['cantidadele'] < $cantidad) {
        throw new Exception('No hay suficiente stock disponible. Stock actual: ' . $elemento['cantidadele']);
    }

    // Iniciar transacción
    $conn->begin_transaction();

    // 1. Crear registro en autorizacion
    $sql_autorizacion = "INSERT INTO autorizacion (
        VoBoCuentadanteaut, nomquienaturiza, cargoquienautoriza, 
        firmaquienautoriza, estadoaut
    ) VALUES (?, ?, ?, ?, ?)";
    
    $VoBo = 'Pendiente';
    $nomQuienAutoriza = 'Sistema';
    $cargo = 'sistema';
    $firmaAutoriza = 'pendiente';
    $estado = 'pendiente';
    
    $stmt_autorizacion = $conn->prepare($sql_autorizacion);
    $stmt_autorizacion->bind_param("sssss", 
        $VoBo, $nomQuienAutoriza, $cargo, $firmaAutoriza, $estado
    );
    
    if (!$stmt_autorizacion->execute()) {
        throw new Exception('Error al crear la autorización: ' . $conn->error);
    }
    
    $idAutorizacion = $conn->insert_id;
    
    // 2. Crear la notificación para el cuentadante
    $sql_notif = "INSERT INTO notificaciones (Tiponot, estadonot, idautori) 
                 VALUES ('peticion_pendiente', 'pendiente', ?)";
    $stmt_notif = $conn->prepare($sql_notif);
    $stmt_notif->bind_param("i", $idAutorizacion);
    
    if (!$stmt_notif->execute()) {
        throw new Exception('Error al crear la notificación: ' . $conn->error);
    }

    // 3. Crear registro en detallesprestamo
    $sql_detalle = "INSERT INTO detallesprestamo (
        descelementodetpre, codigoinvdetpre, estadocaprestamo, 
        estadoeningreso, nombrecuentadante, idelementos
    ) VALUES (?, ?, 'activo', 'inactivo', ?, ?)";
    
    $descripcion = $elemento['nombreele'] . ' - ' . $elemento['descripcionele'];
    $codigoInventario = $elemento['codigoinventario'] ?? '';
    $nombreCuentadante = 'Pendiente de asignación';
    
    $stmt_detalle = $conn->prepare($sql_detalle);
    $stmt_detalle->bind_param("sssi", 
        $descripcion, $codigoInventario, $nombreCuentadante, $idelementos
    );
    
    if (!$stmt_detalle->execute()) {
        throw new Exception('Error al crear el detalle del préstamo: ' . $conn->error);
    }
    
    $idDetalle = $conn->insert_id;
    
    // 4. Insertar la petición en la tabla prestamos
    $sql_prestamo = "INSERT INTO prestamos (
        cantidad, formacionodependencia, cargopre, lugardetraslado, 
        IDdetalle, IDautorizacion, IDelementos, IDpersonas
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_prestamo = $conn->prepare($sql_prestamo);
    $stmt_prestamo->bind_param("isssiiii", 
        $cantidad, $formacionodependencia, $cargopre, $lugardetraslado, 
        $idDetalle, $idAutorizacion, $idelementos, $idpersonas
    );
    
    if (!$stmt_prestamo->execute()) {
        throw new Exception('Error al registrar la petición: ' . $conn->error);
    }

    // 5. Actualizar el stock del elemento
    $nuevo_stock = $elemento['cantidadele'] - $cantidad;
    $sql_actualizar = "UPDATE elementos SET cantidadele = ? WHERE IDele = ?";
    $stmt_actualizar = $conn->prepare($sql_actualizar);
    $stmt_actualizar->bind_param("ii", $nuevo_stock, $idelementos);
    
    if (!$stmt_actualizar->execute()) {
        throw new Exception('Error al actualizar el stock del elemento.');
    }
    
    // Si el stock llega a 0, cambiar estado a 'en prestamo'
    if ($nuevo_stock <= 0) {
        $sql_estado = "UPDATE elementos SET estado = 'en prestamo' WHERE IDele = ?";
        $stmt_estado = $conn->prepare($sql_estado);
        $stmt_estado->bind_param("i", $idelementos);
        $stmt_estado->execute();
    }

    // Si todo salió bien, confirmar la transacción
    $conn->commit();
    
    // Redirigir con mensaje de éxito
    header('Location: inventario.php?success=peticion_enviada');
    exit();
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    // Redirigir con mensaje de error
    $error_msg = urlencode($e->getMessage());
    header('Location: peticion.php?elemento_id=' . $idelementos . '&elemento_nombre=' . urlencode($elemento['nombreele'] ?? '') . '&elemento_codigo=' . urlencode($elemento['codigoinventario'] ?? '') . '&error=' . $error_msg);
    exit();
}
