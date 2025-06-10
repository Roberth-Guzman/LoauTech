<?php
session_start();

// Verificar sesión y rol de usuario
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'usuario') {
    header('Location: ../login.php');
    exit();
}

require_once '../conexion.php';

$error = '';
$success = '';

// Procesar el formulario de registro de elementos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y validar datos
    $nombre = $conn->real_escape_string(trim($_POST['nombre']));
    $codigo = $conn->real_escape_string(trim($_POST['codigo']));
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $descripcion = $conn->real_escape_string(trim($_POST['descripcion']));
    $cantidad = (int)$_POST['cantidad'];
    $fecha = $conn->real_escape_string($_POST['fecha']);

    // Validaciones adicionales
    if (empty($nombre) || strlen($nombre) > 100) {
        $error = "El nombre es requerido y debe tener máximo 100 caracteres";
    } elseif (empty($codigo) || !preg_match('/^[A-Z0-9-]{5,20}$/', $codigo)) {
        $error = "El código debe contener solo letras mayúsculas, números y guiones (5-20 caracteres)";
    } elseif ($cantidad <= 0 || $cantidad > 1000) {
        $error = "La cantidad debe ser entre 1 y 1000";
    } else {
        try {
            $conn->begin_transaction();

            // Insertar el elemento
            $stmt = $conn->prepare("INSERT INTO elementos 
                                   (nombreele, codigoele, categoriaele, estadoele, descripcionele, cantidadele, fecharegistro, idusuario) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiss", $nombre, $codigo, $categoria, $estado, $descripcion, $cantidad, $fecha, $_SESSION['usuario']['id']);
            $stmt->execute();
            $idElemento = $conn->insert_id;
            $stmt->close();

            // Si hay imagen, procesarla
            if (!empty($_FILES['imagen']['name'])) {
                $imagenNombre = guardarImagen($idElemento);
                if ($imagenNombre) {
                    $conn->query("UPDATE elementos SET imagenele = '$imagenNombre' WHERE IDele = $idElemento");
                }
            }

            $conn->commit();
            $success = "Elemento registrado exitosamente";
            
            // Limpiar campos después de registro exitoso
            $_POST = array();
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error al registrar el elemento: " . $e->getMessage();
        }
    }
}

// Función para guardar imágenes
function guardarImagen($idElemento) {
    $directorio = "../uploads/elementos/";
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreArchivo = "elemento_" . $idElemento . "_" . time() . "." . strtolower($extension);
    $rutaCompleta = $directorio . $nombreArchivo;

    // Validar tipo de archivo
    $permitidos = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($extension), $permitidos)) {
        return false;
    }

    // Validar tamaño (máximo 2MB)
    if ($_FILES['imagen']['size'] > 2097152) {
        return false;
    }

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaCompleta)) {
        return $nombreArchivo;
    }
    
    return false;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Elementos - LOAUTECH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .required:after {
            content: " *";
            color: red;
        }
        .preview-img {
            max-width: 200px;
            max-height: 200px;
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Registro de Elementos</h3>
                            <a href="panel-principal.php" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="registroelemento.php" enctype="multipart/form-data" id="formElemento">
                            <div class="row g-3">
                                <!-- Nombre del Elemento -->
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label required">Nombre del Elemento</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                                           required maxlength="100">
                                </div>
                                
                                <!-- Código del Elemento -->
                                <div class="col-md-6">
                                    <label for="codigo" class="form-label required">Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" 
                                           value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>" 
                                           required pattern="[A-Z0-9-]{5,20}" 
                                           title="Solo letras mayúsculas, números y guiones (5-20 caracteres)">
                                </div>
                                
                                <!-- Categoría -->
                                <div class="col-md-4">
                                    <label for="categoria" class="form-label required">Categoría</label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="Tecnología" <?= ($_POST['categoria'] ?? '') === 'Tecnología' ? 'selected' : '' ?>>Tecnología</option>
                                        <option value="Oficina" <?= ($_POST['categoria'] ?? '') === 'Oficina' ? 'selected' : '' ?>>Oficina</option>
                                        <option value="Herramientas" <?= ($_POST['categoria'] ?? '') === 'Herramientas' ? 'selected' : '' ?>>Herramientas</option>
                                        <option value="Mobiliario" <?= ($_POST['categoria'] ?? '') === 'Mobiliario' ? 'selected' : '' ?>>Mobiliario</option>
                                        <option value="Otro" <?= ($_POST['categoria'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                                
                                <!-- Estado -->
                                <div class="col-md-4">
                                    <label for="estado" class="form-label required">Estado</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="Disponible" <?= ($_POST['estado'] ?? '') === 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                                        <option value="En uso" <?= ($_POST['estado'] ?? '') === 'En uso' ? 'selected' : '' ?>>En uso</option>
                                        <option value="Mantenimiento" <?= ($_POST['estado'] ?? '') === 'Mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                                        <option value="Dañado" <?= ($_POST['estado'] ?? '') === 'Dañado' ? 'selected' : '' ?>>Dañado</option>
                                    </select>
                                </div>
                                
                                <!-- Cantidad -->
                                <div class="col-md-4">
                                    <label for="cantidad" class="form-label required">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" 
                                           value="<?= htmlspecialchars($_POST['cantidad'] ?? 1) ?>" 
                                           min="1" max="1000" required>
                                </div>
                                
                                <!-- Fecha de Registro -->
                                <div class="col-md-6">
                                    <label for="fecha" class="form-label required">Fecha de Registro</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" 
                                           value="<?= htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')) ?>" 
                                           required>
                                </div>
                                
                                <!-- Imagen -->
                                <div class="col-md-6">
                                    <label for="imagen" class="form-label">Imagen del Elemento</label>
                                    <input type="file" class="form-control" id="imagen" name="imagen" 
                                           accept="image/jpeg, image/png, image/gif">
                                    <img id="preview" class="img-thumbnail preview-img" src="#" alt="Vista previa">
                                    <small class="text-muted">Formatos aceptados: JPG, PNG, GIF (Máx. 2MB)</small>
                                </div>
                                
                                <!-- Descripción -->
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" 
                                              rows="3" maxlength="500"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                                </div>
                                
                                <!-- Botón de envío -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-4 py-2">
                                        <i class="fas fa-save me-2"></i> Registrar Elemento
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Vista previa de la imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const preview = document.getElementById('preview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                preview.src = '#';
            }
        });

        // Validación del formulario
        document.getElementById('formElemento').addEventListener('submit', function(e) {
            const imagenInput = document.getElementById('imagen');
            if (imagenInput.files.length > 0) {
                const file = imagenInput.files[0];
                const extension = file.name.split('.').pop().toLowerCase();
                const permitidos = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!permitidos.includes(extension)) {
                    e.preventDefault();
                    alert('Solo se permiten imágenes JPG, PNG o GIF');
                    return false;
                }
                
                if (file.size > 2097152) { // 2MB
                    e.preventDefault();
                    alert('La imagen no debe exceder 2MB');
                    return false;
                }
            }
            
            return true;
        });
    </script>
</body>
</html>