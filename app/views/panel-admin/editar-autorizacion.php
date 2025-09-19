<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Editar Autorización</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/includes/sidebar-admin.php'; ?>

        <!-- Contenido Principal -->
        <div class="flex-1 p-10">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Editar Autorización</h1>
                <a href="<?php echo BASE_URL; ?>/admin/almacenes" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>

            <!-- Mensajes de error o éxito -->
            <?php if(isset($_SESSION['mensaje']) && isset($_SESSION['tipo_mensaje'])): ?>
                <div class="mb-4 p-4 rounded <?php echo ($_SESSION['tipo_mensaje'] == 'success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $_SESSION['mensaje']; ?>
                </div>
                <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); ?>
            <?php endif; ?>

            <!-- Formulario de Edición -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
                <form action="<?php echo BASE_URL; ?>/admin/editarAutorizacion/<?php echo $data['id']; ?>" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label for="VoBoCuentadanteaut" class="block text-gray-700 text-sm font-bold mb-2">Visto Bueno Cuentadante:</label>
                                <input type="text" name="VoBoCuentadanteaut" id="VoBoCuentadanteaut" value="<?php echo htmlspecialchars($data['VoBoCuentadanteaut']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            
                            <div class="mb-4">
                                <label for="nomquienaturiza" class="block text-gray-700 text-sm font-bold mb-2">Nombre Autoriza:</label>
                                <input type="text" name="nomquienaturiza" id="nomquienaturiza" value="<?php echo htmlspecialchars($data['nomquienaturiza']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            
                            <div class="mb-4">
                                <label for="cargoquienautoriza" class="block text-gray-700 text-sm font-bold mb-2">Cargo Autoriza:</label>
                                <input type="text" name="cargoquienautoriza" id="cargoquienautoriza" value="<?php echo htmlspecialchars($data['cargoquienautoriza']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label for="firmaquienautoriza" class="block text-gray-700 text-sm font-bold mb-2">Firma Autoriza:</label>
                                <input type="text" name="firmaquienautoriza" id="firmaquienautoriza" value="<?php echo htmlspecialchars($data['firmaquienautoriza']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            
                            <div class="mb-4">
                                <label for="estadoaut" class="block text-gray-700 text-sm font-bold mb-2">Estado:</label>
                                <select name="estadoaut" id="estadoaut" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="pendiente" <?php echo ($data['estadoaut'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="pendiente_almacen" <?php echo ($data['estadoaut'] == 'pendiente_almacen') ? 'selected' : ''; ?>>Pendiente Almacén</option>
                                    <option value="activo" <?php echo ($data['estadoaut'] == 'activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="aprobado" <?php echo ($data['estadoaut'] == 'aprobado') ? 'selected' : ''; ?>>Aprobado</option>
                                    <option value="rechazado" <?php echo ($data['estadoaut'] == 'rechazado') ? 'selected' : ''; ?>>Rechazado</option>
                                    <option value="inactivo" <?php echo ($data['estadoaut'] == 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                    <option value="en_prestamo" <?php echo ($data['estadoaut'] == 'en_prestamo') ? 'selected' : ''; ?>>En Préstamo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>