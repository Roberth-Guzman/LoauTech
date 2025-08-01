<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit();
}

require_once '../conexion.php';

$error = '';
$success = '';
$usuario_id = $_SESSION['usuario']['IDper'] ?? $_SESSION['usuario']['idper'] ?? null;
$usuario_documento = $_SESSION['usuario']['documento'] ?? 'N/A';

if (!$usuario_id) {
    $error = "Error: No se pudo obtener el ID del usuario. Por favor, cierre sesión y vuelva a iniciar.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario_id) {
    $nombre = $conn->real_escape_string(trim($_POST['nombreingele']));
    $tipo = $conn->real_escape_string(trim($_POST['tipoelemento']));
    $descripcion = $conn->real_escape_string(trim($_POST['descripcioningele']));
    $observacion = $conn->real_escape_string(trim($_POST['observacioningele']));
    $serial = $conn->real_escape_string(trim($_POST['serial']));
    $idper = $usuario_id;

    if (empty($nombre) || strlen($nombre) > 250) {
        $error = "El nombre es requerido y debe tener máximo 250 caracteres";
    } elseif (empty($tipo)) {
        $error = "Debe seleccionar un tipo de elemento";
    } elseif (empty($serial) || strlen($serial) > 100) {
        $error = "El serial es requerido y debe tener máximo 100 caracteres";
    } else {
        try {
            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO ingresoelementos 
                (nombreingele, tipoelemento, descripcioningele, observacioningele, serial, hora_entrada, hora_salida, IDPER) 
                VALUES (?, ?, ?, ?, ?, NOW(), NULL, ?)");
            $stmt->bind_param("sssssi", $nombre, $tipo, $descripcion, $observacion, $serial, $idper);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            $success = "Elemento registrado exitosamente";
            $_POST = array();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error al registrar el elemento: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Elementos - LOAUTECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="flex h-screen">
    <!-- Sidebar -->
    <?php include __DIR__ . '/includes/usuario-sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="flex-1 ml-64 overflow-auto">
        <div class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">Registro de Elementos</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        <?= ucfirst(htmlspecialchars($_SESSION['usuario']['rol'] ?? 'usuario')); ?>
                    </span>
                </div>
            </div>
        </div>

        <main class="p-6">
            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded"><?= $success ?></div>
            <?php endif; ?>

            <!-- Info del usuario -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Información del Usuario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nombre</p>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? '') ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Documento</p>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($usuario_documento) ?></p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Datos del Elemento</h3>
                    <p class="text-sm text-gray-500">Complete todos los campos obligatorios (*)</p>
                </div>

                <form method="POST" class="p-6 space-y-6">
                    <div>
                        <label class="block font-semibold text-sm text-gray-700" for="nombreingele">Nombre del Elemento *</label>
                        <input type="text" id="nombreingele" name="nombreingele" required
                               value="<?= htmlspecialchars($_POST['nombreingele'] ?? '') ?>"
                               class="w-full border border-gray-300 px-4 py-2 rounded shadow-sm focus:ring focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block font-semibold text-sm text-gray-700" for="tipoelemento">Tipo de Elemento *</label>
                        <select id="tipoelemento" name="tipoelemento" required
                                class="w-full border border-gray-300 px-4 py-2 rounded shadow-sm focus:ring focus:ring-blue-200">
                            <option value="" disabled selected>Seleccione una categoría</option>
                            <?php
                            $categorias = ['Computadores', 'Monitores', 'Teclados', 'Mouse', 'Impresoras', 'Muebles', 'Otros'];
                            foreach ($categorias as $cat):
                                $selected = ($_POST['tipoelemento'] ?? '') === $cat ? 'selected' : '';
                                echo "<option value=\"$cat\" $selected>$cat</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-sm text-gray-700" for="descripcioningele">Descripción</label>
                        <textarea id="descripcioningele" name="descripcioningele" rows="3"
                                  class="w-full border border-gray-300 px-4 py-2 rounded shadow-sm focus:ring focus:ring-blue-200"><?= htmlspecialchars($_POST['descripcioningele'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-sm text-gray-700" for="serial">Serial *</label>
                        <input type="text" id="serial" name="serial" required
                               value="<?= htmlspecialchars($_POST['serial'] ?? '') ?>"
                               class="w-full border border-gray-300 px-4 py-2 rounded shadow-sm focus:ring focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block font-semibold text-sm text-gray-700" for="observacioningele">Observaciones</label>
                        <textarea id="observacioningele" name="observacioningele" rows="2"
                                  class="w-full border border-gray-300 px-4 py-2 rounded shadow-sm focus:ring focus:ring-blue-200"><?= htmlspecialchars($_POST['observacioningele'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="panel-principal.php" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">Cancelar</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                            Registrar Elemento
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <footer class="bg-white border-t mt-8">
            <div class="max-w-7xl mx-auto px-4 py-4 text-center text-sm text-gray-500">
                &copy; <?= date('Y') ?> LOAUTECH - Todos los derechos reservados
            </div>
        </footer>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
