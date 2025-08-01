<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

include '../conexion.php';

$idUsuario = $_SESSION['usuario']['IDper'];

// Consulta para obtener los datos del usuario
$sql = "SELECT 
            p.nombrecompletoper, 
            p.tipodocumento, 
            p.numerodoc,
            c.correocont, 
            c.numerocont,
            r.rol,
            fp.ruta AS foto_ruta
        FROM personas p
        LEFT JOIN contactos c ON p.IDper = c.IDperso
        LEFT JOIN roles r ON p.IDper = r.idper
        LEFT JOIN fotos_perfil fp ON p.IDper = fp.id_persona AND fp.es_actual = 1
        WHERE p.IDper = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("No se encontró información del usuario.");
}

// Mostrar mensajes de éxito o error
if (isset($_SESSION['exito'])) {
    $mensaje_exito = $_SESSION['exito'];
    unset($_SESSION['exito']);
}
if (isset($_SESSION['error'])) {
    $mensaje_error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Ruta base para fotos
define('FOTOS_PERFIL_DIR', '../uploads/fotos_perfil/');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Perfil de Usuario - LOAUTECH</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="flex h-screen">
     <?php include __DIR__ . '/includes/usuario-sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="flex-1 ml-64 overflow-auto">
      <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
          <h1 class="text-xl font-bold text-gray-900">Perfil de Usuario</h1>
          <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
            <?= ucfirst(htmlspecialchars($usuario['rol'] ?? 'usuario')) ?>
          </span>
        </div>
      </div>

      <?php if (!empty($mensaje_exito)): ?>
      <div class="max-w-4xl mx-auto mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
          <?= htmlspecialchars($mensaje_exito) ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!empty($mensaje_error)): ?>
      <div class="max-w-4xl mx-auto mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
          <?= htmlspecialchars($mensaje_error) ?>
        </div>
      </div>
      <?php endif; ?>

      <main class="p-6">
        <div class="max-w-4xl mx-auto">
          <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Información del Perfil</h3>
              <p class="mt-1 text-sm text-gray-500">Detalles personales y datos de contacto.</p>
            </div>
            <div class="px-6 py-4">
              <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-6">
                <!-- Foto de perfil -->
                <div class="flex-shrink-0">
                  <div class="relative group">
                    <?php 
                      $foto = $usuario['foto_ruta'] ?? null;
                      $foto_path = FOTOS_PERFIL_DIR . basename($foto);
                      if ($foto && file_exists($foto_path)): 
                    ?>
                      <img src="../<?= htmlspecialchars($foto) ?>" alt="Foto de perfil" class="h-40 w-40 rounded-full object-cover border-4 border-white shadow-md">
                    <?php else: ?>
                      <div class="h-40 w-40 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-user text-gray-400 text-6xl"></i>
                      </div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                      <div class="flex space-x-2">
                        <a href="#" class="bg-white p-2 rounded-full text-gray-800 hover:bg-gray-100" data-bs-toggle="modal" data-bs-target="#modalSubirFoto" title="Cambiar foto"><i class="fas fa-camera"></i></a>
                        <?php if ($foto): ?>
                        <a href="foto/borrar-foto.php" class="bg-white p-2 rounded-full text-red-600 hover:bg-red-50" onclick="return confirm('¿Estás seguro de que quieres eliminar tu foto de perfil?');" title="Eliminar foto"><i class="fas fa-trash"></i></a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="mt-4 text-center">
                    <button type="button" class="text-sm text-blue-600 hover:text-blue-800 font-medium" data-bs-toggle="modal" data-bs-target="#modalSubirFoto">
                      Cambiar foto
                    </button>
                  </div>
                </div>

                <!-- Información del usuario -->
                <div class="flex-1">
                  <div class="space-y-4">
                    <div>
                      <h4 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($usuario['nombrecompletoper'] ?? '') ?></h4>
                      <p class="text-sm text-gray-500"><?= ucfirst(htmlspecialchars($usuario['rol'] ?? 'usuario')) ?></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div><p class="text-sm font-medium text-gray-500">Tipo de Documento</p><p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['tipodocumento'] ?? 'No especificado') ?></p></div>
                      <div><p class="text-sm font-medium text-gray-500">Número de Documento</p><p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['numerodoc'] ?? 'No especificado') ?></p></div>
                      <div><p class="text-sm font-medium text-gray-500">Correo Electrónico</p><p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['correocont'] ?? 'No especificado') ?></p></div>
                      <div><p class="text-sm font-medium text-gray-500">Teléfono</p><p class="text-sm text-gray-900"><?= htmlspecialchars($usuario['numerocont'] ?? 'No especificado') ?></p></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
              <a href="editar-perfil.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"><i class="fas fa-edit mr-2"></i>Editar Perfil</a>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal de subida de foto -->
  <div class="modal fade" id="modalSubirFoto" tabindex="-1" aria-labelledby="modalSubirFotoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="foto/subir-foto.php" method="post" enctype="multipart/form-data" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSubirFotoLabel">Cambiar Foto de Perfil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <input type="file" name="foto_perfil" accept="image/*" required class="form-control">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Subir Foto</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
