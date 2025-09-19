<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data['titulo']); ?> - LOAUTECH</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Estilos para la impresión */
        @media print {
            /* Oculta elementos que no deben imprimirse, como la barra lateral y los botones */
            .print-hide {
                display: none !important;
            }
            /* Asegura que el contenido principal ocupe toda la página al imprimir */
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            /* Ajustes para que el fondo y los colores se impriman correctamente */
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex">
        
        <!-- Incluir la barra lateral específica de portería -->
        <div class="print-hide">
            <?php require_once 'includes/sidebar-porteria.php'; ?>
        </div>

        <!-- Contenido Principal -->
        <div class="flex-1 p-4 sm:ml-64 main-content">
            <div class="container mx-auto px-4 py-8">
                <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                    <div id="comprobante-imprimible">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <img src="mvc_dev/public/img/logo_loautech.png" alt="Logo" class="h-12">
                                    <h1 class="text-2xl font-bold text-gray-800 mt-2">Comprobante de Salida</h1>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600">ID Préstamo: <span class="font-semibold"><?php echo htmlspecialchars($data['comprobante']->IDpre); ?></span></p>
                                    <p class="text-gray-600">Fecha Salida: <span class="font-semibold"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($data['comprobante']->fecha_salida))); ?></span></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Información del Solicitante -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-3">Información del Solicitante</h2>
                                    <p><strong class="text-gray-600">Nombre:</strong> <?php echo htmlspecialchars($data['comprobante']->nombrecompletoper); ?></p>
                                    <p><strong class="text-gray-600">Documento:</strong> <?php echo htmlspecialchars($data['comprobante']->tipodocumento) . ' ' . htmlspecialchars($data['comprobante']->numerodoc); ?></p>
                                    <p><strong class="text-gray-600">Formación/Dependencia:</strong> <?php echo htmlspecialchars($data['comprobante']->formacionodependencia); ?></p>
                                    <p><strong class="text-gray-600">Cargo:</strong> <?php echo htmlspecialchars($data['comprobante']->cargopre); ?></p>
                                </div>

                                <!-- Información del Elemento -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-3">Información del Elemento</h2>
                                    <p><strong class="text-gray-600">Nombre:</strong> <?php echo htmlspecialchars($data['comprobante']->nombreele); ?></p>
                                    <p><strong class="text-gray-600">Código de Inventario:</strong> <?php echo htmlspecialchars($data['comprobante']->codigoinventario); ?></p>
                                    <p><strong class="text-gray-600">Descripción:</strong> <?php echo htmlspecialchars($data['comprobante']->descripcionele); ?></p>
                                    <p><strong class="text-gray-600">Lugar de Traslado:</strong> <?php echo htmlspecialchars($data['comprobante']->lugardetraslado); ?></p>
                                </div>
                            </div>

                            <!-- Observaciones y Firmas -->
                            <div class="mt-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Observaciones de Salida</h2>
                                    <p class="text-gray-800"><?php echo !empty($data['comprobante']->observaciones_salida) ? htmlspecialchars($data['comprobante']->observaciones_salida) : 'Sin observaciones.'; ?></p>
                                </div>
                            </div>

                            <div class="mt-12 pt-6 border-t-2 border-dashed text-center">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="border-t-2 border-gray-400 mt-12 mx-auto w-3/4"></p>
                                        <p class="mt-2 text-sm font-semibold">Firma del Solicitante</p>
                                    </div>
                                    <div>
                                        <p class="border-t-2 border-gray-400 mt-12 mx-auto w-3/4"></p>
                                        <p class="mt-2 text-sm font-semibold">Firma del Vigilante</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción (se ocultan al imprimir) -->
                    <div class="p-6 bg-gray-100 flex justify-end space-x-4 print-hide">
                        <a href="<?php echo BASE_URL; ?>/porteria/peticiones" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>Regresar
                        </a>
                        <button onclick="imprimirComprobante()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                            <i class="fas fa-print mr-2"></i>Imprimir / Guardar PDF
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Incluir el pie de página específico de portería -->
            <div class="print-hide">
                <?php require_once 'includes/footer-porteria.php'; ?>
            </div>
        </div>
    </div>

    <script>
        function imprimirComprobante() {
            window.print();
        }
    </script>

</body>
</html>