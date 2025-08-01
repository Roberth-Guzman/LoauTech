<?php
// Conexión a la base de datos
require_once $_SERVER['DOCUMENT_ROOT'] . '/loautech-main/conexion.php';

// Validar que se recibieron todos los datos requeridos del formulario
if (
    !isset($_POST['nombre']) || !isset($_POST['tipoDocumento']) || !isset($_POST['numeroDocumento']) ||
    !isset($_POST['equipo']) || !isset($_POST['cargo']) || !isset($_POST['lugardetraslado']) ||
    !isset($_POST['formodep']) || !isset($_POST['cantidad']) || !isset($_POST['mensaje'])
) {
    echo "Error: Faltan campos del formulario.";
    exit();
}

// Capturar y sanitizar los datos del formulario
$nombre      = trim($_POST['nombre']);
$tipo_doc    = trim($_POST['tipoDocumento']);
$num_doc     = trim($_POST['numeroDocumento']);
$equipo      = trim($_POST['equipo']);
$cargo       = trim($_POST['cargo']);
$lugar       = trim($_POST['lugardetraslado']);
$formacion   = trim($_POST['formodep']);
$cantidad    = (int) $_POST['cantidad'];
$mensaje     = trim($_POST['mensaje']);

// 🔎 Obtener ID de la persona según su número de documento
$sqlPersona = $conn->prepare("SELECT IDper FROM personas WHERE numerodoc = ?");
$sqlPersona->bind_param("i", $num_doc);
$sqlPersona->execute();
$res = $sqlPersona->get_result();

if ($res->num_rows === 0) {
    echo "Error: Usuario no encontrado";
    exit();
}

$row = $res->fetch_assoc();
$idPersona = $row['IDper'];

// 🔎 Buscar el ID del elemento (IDele) según el nombre seleccionado en el formulario
$sqlElemento = $conn->prepare("SELECT IDele FROM elementos WHERE nombreele = ?");
$sqlElemento->bind_param("s", $equipo);
$sqlElemento->execute();
$resElem = $sqlElemento->get_result();

if ($resElem->num_rows === 0) {
    echo "Error: El elemento '$equipo' no existe en la tabla 'elementos'.";
    exit();
}

$rowElem = $resElem->fetch_assoc();
$idElemento = $rowElem['IDele'];

// 🧾 Insertar en detallesprestamo
$codigoInventario = rand(1000, 9999); // Simula un código interno aleatorio

$stmt1 = $conn->prepare("INSERT INTO detallesprestamo 
(cantidaddetpre, descelementodetpre, codigoinvdetpre, estadocaprestamo, estadoeningreso, nombrecuentadante)
VALUES (?, ?, ?, 'activo', 'activo', ?)");
$stmt1->bind_param("isis", $cantidad, $equipo, $codigoInventario, $nombre);

if (!$stmt1->execute()) {
    echo "Error al guardar detalle del préstamo: " . $stmt1->error;
    exit();
}

$idDetalle = $stmt1->insert_id;

// 📦 Insertar en prestamos con IDautorizacion = NULL y con IDelementos
$stmt2 = $conn->prepare("INSERT INTO prestamos 
(nomquienaturiza, formacionodependencia, cargopre, lugardetraslado, IDdetalle, IDpersonas, IDelementos, IDautorizacion, fecha_prestamo)
VALUES (?, ?, ?, ?, ?, ?, ?, NULL, NOW())");
$stmt2->bind_param("ssssiii", $nombre, $formacion, $cargo, $lugar, $idDetalle, $idPersona, $idElemento);

if (!$stmt2->execute()) {
    echo "Error al guardar la solicitud de préstamo: " . $stmt2->error;
    exit();
}

echo "✅ Solicitud registrada con éxito.";
?>
