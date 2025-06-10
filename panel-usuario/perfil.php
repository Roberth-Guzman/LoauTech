<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Loautech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100 d-flex flex-column p-0">
        <!-- Header -->
        <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <a href="panel-principal.php" class="text-white fs-4 text-decoration-none">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="mb-0 fs-4 fw-bold text-center flex-grow-1">PERFIL DE USUARIO</h1>
            <a href="#" class="text-white fs-4 text-decoration-none">
                <i class="bi bi-share"></i>
            </a>
        </div>

        <!-- Contenido principal -->
        <div class="container py-4 flex-grow-1 overflow-auto">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <!-- Sección de avatar -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <div class="border border-3 border-dark rounded-circle p-1 mx-auto" style="width: 150px; height: 150px;">
                                <div class="bg-light rounded-circle d-flex justify-content-center align-items-center w-100 h-100">
                                    <i class="bi bi-person text-secondary" style="font-size: 80px;"></i>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-secondary rounded-circle position-absolute bottom-0 end-0">
                                <i class="bi bi-camera"></i>
                            </button>
                        </div>
                        <h5 class="mt-3 fw-bold">*NOMBRE DEL USUARIO*</h5>
                        <p class="text-muted mb-0">correo@ejemplo.com</p>
                    </div>

                    <!-- Información personal -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-4">INFORMACIÓN PERSONAL</h5>
                        
                        <div class="row g-4">
                            <!-- Columna izquierda -->
                            <div class="col-md-6">
                                <!-- Tipo de identidad -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Tipo de Identidad</small>
                                        <div class="border-bottom pb-1">-- --</div>
                                    </div>
                                </div>
                                
                                <!-- Número de Identidad -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Número de Identidad</small>
                                        <div class="border-bottom pb-1">--- --- ----</div>
                                    </div>
                                </div>
                                
                                <!-- Nombre -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Nombre</small>
                                        <div class="border-bottom pb-1">------ ----- ---</div>
                                    </div>
                                </div>
                                
                                <!-- Correo -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Correo</small>
                                        <div class="border-bottom pb-1">------------</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Columna derecha -->
                            <div class="col-md-6">
                                <!-- Titulación -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Titulación</small>
                                        <div class="border-bottom pb-1">-- -- --</div>
                                    </div>
                                </div>
                                
                                <!-- Fecha -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Fecha</small>
                                        <div class="border-bottom pb-1">------</div>
                                    </div>
                                </div>
                                
                                <!-- Teléfono -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Teléfono</small>
                                        <div class="border-bottom pb-1">--- --- ----</div>
                                    </div>
                                </div>
                                
                                <!-- Contraseña -->
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2">
                                        <i class="bi bi-pencil-fill text-warning"></i>
                                    </span>
                                    <div class="w-100">
                                        <small class="text-muted d-block">Contraseña</small>
                                        <div class="border-bottom pb-1">----------</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón de editar -->
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">
                            <i class="bi bi-pencil-square me-2"></i>Editar Perfil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para el botón de retroceso
        document.getElementById('back-button').addEventListener('click', function() {
            window.history.back();
        });
    </script>
</body>
</html>