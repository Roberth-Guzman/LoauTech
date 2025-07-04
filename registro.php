<?php
// Iniciar sesión al principio
session_start();

include 'conexion.php';

// Verificar si ya está logueado
if (isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger y sanitizar datos del formulario
    $tipoIdentidad = $conn->real_escape_string($_POST['tipoIdentidad']);
    $numeroIdentidad = $conn->real_escape_string($_POST['numeroIdentidad']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = (int)$_POST['rol']; // Convertir a entero para seguridad

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // 1. Insertar en tabla personas
        $sqlPersona = "INSERT INTO personas (nombrecompletoper, tipodocumento, numerodoc) 
                       VALUES (?, ?, ?)";
        $stmtPersona = $conn->prepare($sqlPersona);
        $stmtPersona->bind_param("sss", $nombre, $tipoIdentidad, $numeroIdentidad);
        $stmtPersona->execute();
        $idPersona = $conn->insert_id;
        $stmtPersona->close();

        // 2. Determinar nombre del rol
        $roles = [
            1 => 'usuario',
            2 => 'porteria', 
            3 => 'admin'
        ];
        $rolNombre = $roles[$rol] ?? 'usuario';

        // Insertar en tabla roles
        $sqlRol = "INSERT INTO roles (rol, estadorol, idper) 
                   VALUES (?, 'activo', ?)";
        $stmtRol = $conn->prepare($sqlRol);
        $stmtRol->bind_param("si", $rolNombre, $idPersona);
        $stmtRol->execute();
        $stmtRol->close();

        // 3. Insertar en tabla cuentas
        $sqlCuenta = "INSERT INTO cuentas (numerodoc, contracue, estadocue) 
                      VALUES (?, ?, 'activo')";
        $stmtCuenta = $conn->prepare($sqlCuenta);
        $stmtCuenta->bind_param("ss", $numeroIdentidad, $contrasena);
        $stmtCuenta->execute();
        $stmtCuenta->close();

        // 4. Insertar en tabla contactos
        $sqlContacto = "INSERT INTO contactos (numerocont, direccioncont, correocont, estadocont, IDperso) 
                        VALUES (?, ?, ?, 'activo', ?)";
        $stmtContacto = $conn->prepare($sqlContacto);
        $stmtContacto->bind_param("sssi", $telefono, $direccion, $correo, $idPersona);
        $stmtContacto->execute();
        $stmtContacto->close();

        // Confirmar transacción
        $conn->commit();

        // Establecer sesión automáticamente
        $_SESSION['usuario'] = [
            'id' => $idPersona,
            'nombre' => $nombre,
            'documento' => $numeroIdentidad,
            'rol' => $rolNombre
        ];

        // Redirigir según el rol con verificaciones
        $redirecciones = [
            'admin' => 'panel-admin/panel-principal.php',
            'porteria' => 'panel-porteria/panel-principal.php',
            'usuario' => 'panel-usuario/panel-principal.php'
        ];

        // Verificar si la ruta existe antes de redirigir
        $ruta = $redirecciones[$rolNombre] ?? 'login.php?registro=exito';
        
        // Depuración: Registrar la ruta a la que se redirigirá
        error_log("Redireccionando a: $ruta para el rol: $rolNombre");
        
        // Verificar si el archivo existe (opcional, solo para depuración)
        if (file_exists($ruta)) {
            error_log("El archivo $ruta existe");
        } else {
            error_log("El archivo $ruta NO existe, redirigiendo a login");
            $ruta = 'login.php?registro=exito';
        }

        header("Location: $ruta");
        exit();

    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        $error = "Error en el registro: " . $e->getMessage();
        error_log($error); // Registrar el error
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .input-icon {
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="bg-blue-600 py-6 px-8 text-center">
                <h1 class="text-2xl font-bold text-white">LOAUTECH</h1>
                <p class="text-blue-100 mt-1">Crear nueva cuenta</p>
            </div>
            
            <!-- Contenido del formulario -->
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Registro de Usuario</h2>
                
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-2 gap-6" id="formRegistro">
                    <!-- Columna 1 -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de identidad *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                                <select 
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="tipoIdentidad" 
                                    required>
                                    <option value="" selected disabled>Seleccione...</option>
                                    <option value="CC">Cédula de Ciudadanía</option>
                                    <option value="TI">Tarjeta de Identidad</option>
                                    <option value="CE">Cédula de Extranjería</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de identidad *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="numeroIdentidad" 
                                    placeholder="Ej: 1234567890" 
                                    pattern="[0-9]{6,12}"
                                    title="Ingrese un número de identificación válido (6-12 dígitos)"
                                    required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="nombre" 
                                    placeholder="Nombre completo" 
                                    pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]{5,100}"
                                    title="Ingrese un nombre válido (solo letras y espacios)"
                                    required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna 2 -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input 
                                    type="email" 
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="correo" 
                                    placeholder="ejemplo@email.com" 
                                    required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input 
                                    type="tel" 
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="telefono" 
                                    placeholder="Ej: 3001234567" 
                                    pattern="[0-9]{10,15}"
                                    title="Ingrese un número de teléfono válido (10-15 dígitos)"
                                    required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input 
                                    type="text" 
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="direccion" 
                                    placeholder="Ej: Cra 10 #20-30, Neiva" 
                                    required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos de ancho completo -->
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="contrasena"
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="contrasena" 
                                    placeholder="Crea una contraseña segura" 
                                    minlength="8"
                                    required>
                                <div class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="confirmar_contrasena"
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="Repite tu contraseña" 
                                    minlength="8"
                                    required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de usuario *</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="fas fa-user-tag text-gray-400"></i>
                                </div>
                                <select 
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    name="rol" 
                                    required>
                                    <option value="1">Usuario Normal</option>
                                    <option value="2">Portería</option>
                                    <option value="3">Administrador</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button 
                                type="submit" 
                                class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <i class="fas fa-user-plus mr-2"></i>
                                Registrarse
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        ¿Ya tienes cuenta? 
                        <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            Inicia sesión aquí
                        </a>
                    </p>
                </div>
            </div>
            
            <!-- Pie de página -->
            <div class="bg-gray-50 px-8 py-4 text-center">
                <p class="text-xs text-gray-500">
                    &copy; <?= date('Y') ?> LOAUTECH. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Validación de contraseñas coincidentes
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            const contrasena = document.getElementById('contrasena');
            const confirmar = document.getElementById('confirmar_contrasena');
            
            if (contrasena.value !== confirmar.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                confirmar.focus();
            }
        });
    </script>
</body>
</html>