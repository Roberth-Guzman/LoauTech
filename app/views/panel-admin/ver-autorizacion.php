<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Ver Autorización</title>
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
                <h1 class="text-3xl font-bold">Ver Autorización</h1>
                <a href="<?php echo BASE_URL; ?>/admin/almacenes" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>

            <!-- Detalles de la Autorización -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">ID:</p>
                        <p class="text-lg mb-4"><?php echo htmlspecialchars($data['autorizacion']->IDaut); ?></p>
                        
                        <p class="text-gray-600 text-sm font-semibold mb-1">Visto Bueno Cuentadante:</p>
                        <p class="text-lg mb-4"><?php echo htmlspecialchars($data['autorizacion']->VoBoCuentadanteaut); ?></p>
                        
                        <p class="text-gray-600 text-sm font-semibold mb-1">Nombre Autoriza:</p>
                        <p class="text-lg mb-4"><?php echo htmlspecialchars($data['autorizacion']->nomquienaturiza); ?></p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600 text-sm font-semibold mb-1">Cargo Autoriza:</p>
                        <p class="text-lg mb-4"><?php echo htmlspecialchars($data['autorizacion']->cargoquienautoriza); ?></p>
                        
                        <p class="text-gray-600 text-sm font-semibold mb-1">Firma Autoriza:</p>
                        <p class="text-lg mb-4"><?php echo htmlspecialchars($data['autorizacion']->firmaquienautoriza); ?></p>
                        
                        <p class="text-gray-600 text-sm font-semibold mb-1">Estado:</p>
                        <p class="text-lg mb-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?php 
                                switch($data['autorizacion']->estadoaut) {
                                    case 'activo':
                                    case 'aprobado':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'pendiente':
                                    case 'pendiente_almacen':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'rechazado':
                                    case 'inactivo':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                    case 'en_prestamo':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    default:
                                        echo 'bg-gray-100 text-gray-800';
                                }
                            ?>">
                                <?php echo htmlspecialchars($data['autorizacion']->estadoaut); ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="mt-8 flex space-x-4">
                    <a href="<?php echo BASE_URL; ?>/admin/editarAutorizacion/<?php echo $data['autorizacion']->IDaut; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                    <a href="<?php echo BASE_URL; ?>/admin/eliminarAutorizacion/<?php echo $data['autorizacion']->IDaut; ?>" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('¿Está seguro de eliminar esta autorización?');">
                        <i class="fas fa-trash mr-2"></i>Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>