<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Loautech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light"></body>
<div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow-lg w-100" style="max-width: 1000px;">
        <!-- Encabezado con flecha integrada -->
        <div class="card-header bg-primary text-white py-3">
            <div class="row align-items-center g-0">
                <div class="col-auto">
                    <a href="panel-principal.php" class="text-white text-decoration-none">
                        <i class="bi bi-arrow-left-circle-fill" style="font-size: 1.8rem;"></i>
                    </a>
                </div>
                <div class="col text-center">
                    <h2 class="card-title mb-0">SOLICITUD DE EQUIPOS</h2>
                </div>
                <div class="col-auto" style="width: 48px;"><!-- Espacio compensatorio --></div>
            </div>
        </div>

        <div class="card-body p-4 p-lg-5">
            <form>
                <!-- Campos del formulario (igual que versión anterior) -->
                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="nombre" class="form-label fw-bold">Nombre:</label>
                        <input type="text" class="form-control form-control-lg" id="nombre">
                    </div>
                    <div class="col-md-3">
                        <label for="tipoDocumento" class="form-label fw-bold">Tipo de documento</label>
                        <select class="form-select form-select-lg" id="tipoDocumento">
                            <option>DNI</option>
                            <option>Pasaporte</option>
                            <option>Carnet de extranjería</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="numeroDocumento" class="form-label fw-bold">Número de documento</label>
                        <input type="text" class="form-control form-control-lg" id="numeroDocumento">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="equipo" class="form-label fw-bold">Seleccionar un equipo</label>
                        <select class="form-select form-select-lg" id="equipo">
                            <option>Laptop</option>
                            <option>...</option>
                            <option>...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label fw-bold">Cargo</label>
                        <input type="text" class="form-control form-control-lg" id="cargo">
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label fw-bold">Lugar de traslado</label>
                        <input type="text" class="form-control form-control-lg" id="lugardetraslado">
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label fw-bold">Formacion o Dependencia</label>
                        <input type="text" class="form-control form-control-lg" id="formodep">
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label fw-bold">Cantidad</label>
                        <input type="number" class="form-control form-control-lg" id="cantidad">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="mensaje" class="form-label fw-bold">Mensaje Adicional</label>
                    <textarea class="form-control form-control-lg" id="mensaje" rows="3"></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary btn-lg px-4 me-md-2">
                        <i class="bi bi-send-fill me-2"></i>Enviar
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-x-circle me-2"></i>Limpiar
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card-footer bg-light py-3">
            <p class="text-muted text-center mb-0 fw-bold">DATOS TÉCNICOS</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>