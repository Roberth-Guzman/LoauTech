<?php
session_start();
include '../conexion.php';

// Verificar si el usuario es de portería
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['user']['rol'] !== 'porteria') {
    header("Location: ../panel-usuario/panel-principal.php");
    exit;
}

// Procesar código escaneado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo'])) {
    $codigo = $conn->real_escape_string($_POST['codigo']);
    
    // Buscar el código en la base de datos
    $sql = "SELECT p.*, c.numerocont, c.correocont, r.rol 
            FROM codigos_barras cb
            JOIN personas p ON cb.idperlas = p.IDper
            JOIN contactos c ON p.IDper = c.IDperso
            JOIN roles r ON p.IDper = r.idper
            WHERE cb.codigo = '$codigo'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $persona = $result->fetch_assoc();
        
        // Registrar entrada/salida en marcador
        $sql_marcador = "SELECT * FROM marcador 
                         WHERE IDmar IN (
                             SELECT IDmarcador FROM ingresoelementos 
                             WHERE IDPER = {$persona['IDper']}
                         ) ORDER BY IDmar DESC LIMIT 1";
        
        $marcador = $conn->query($sql_marcador)->fetch_assoc();
        
        if ($marcador && $marcador['horaentradamar'] && !$marcador['horasalidamar']) {
            // Registrar salida
            $conn->query("UPDATE marcador SET horasalidamar = NOW() 
                          WHERE IDmar = {$marcador['IDmar']}");
            echo json_encode(['tipo' => 'salida', 'persona' => $persona]);
        } else {
            // Registrar entrada
            $conn->query("INSERT INTO marcador (horaentradamar, fechamar, estadomar) 
                          VALUES (NOW(), CURDATE(), 'activo')");
            $id_marcador = $conn->insert_id;
            
            // Relacionar con el elemento si existe
            $conn->query("UPDATE ingresoelementos SET IDmarcador = $id_marcador 
                          WHERE IDPER = {$persona['IDper']} AND IDmarcador IS NULL");
            
            echo json_encode(['tipo' => 'entrada', 'persona' => $persona]);
        }
    } else {
        echo json_encode(['error' => 'Código no registrado']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escáner - Loautech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
    <style>
        #scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            height: 300px;
            margin: 0 auto;
            border: 3px solid #0d6efd;
            border-radius: 8px;
            overflow: hidden;
        }
        #scanner-line {
            position: absolute;
            height: 3px;
            background: #dc3545;
            width: 100%;
            animation: scan 2s infinite linear;
        }
        @keyframes scan {
            0% { top: 0; }
            100% { top: 100%; }
        }
        #resultado {
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-3 mt-md-5">
        <div class="card mx-auto shadow" style="max-width: 600px;">
            <div class="card-header bg-primary text-white text-center py-3">
                <h1 class="card-title h4 mb-0">ESCANER DE CÓDIGO DE BARRAS</h1>
            </div>
            <div class="card-body text-center p-4">
                <div class="mb-4">
                    <!-- Contenedor de la cámara -->
                    <div id="scanner-container">
                        <div id="interactive" class="viewport"></div>
                        <div id="scanner-line"></div>
                    </div>
                    
                    <!-- Resultado del escaneo -->
                    <div id="resultado" class="mt-3 p-3 bg-light rounded">
                        <h5 class="text-primary">Resultado:</h5>
                        <p id="nombre-persona"></p>
                        <p id="documento-persona"></p>
                        <p id="tipo-registro" class="fw-bold"></p>
                    </div>
                    
                    <!-- Mensajes -->
                    <div id="no-camera-warning" class="alert alert-warning mt-3 d-none">
                        No se encontró una cámara disponible.
                    </div>
                    <div id="error-message" class="alert alert-danger mt-3 d-none"></div>
                </div>
                
                <div class="d-flex justify-content-center gap-3">
                    <button id="startScannerBtn" class="btn btn-primary btn-lg px-4 py-2">
                        <i class="bi bi-camera me-2"></i>INICIAR ESCANER
                    </button>
                    <button id="stopScannerBtn" class="btn btn-danger btn-lg px-4 py-2 d-none">
                        <i class="bi bi-stop-circle me-2"></i>DETENER ESCANER
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const startBtn = document.getElementById('startScannerBtn');
        const stopBtn = document.getElementById('stopScannerBtn');
        const scannerContainer = document.getElementById('scanner-container');
        const noCameraWarning = document.getElementById('no-camera-warning');
        const resultadoDiv = document.getElementById('resultado');
        const nombrePersona = document.getElementById('nombre-persona');
        const documentPersona = document.getElementById('documento-persona');
        const tipoRegistro = document.getElementById('tipo-registro');
        const errorMessage = document.getElementById('error-message');
        
        let scannerActive = false;
        
        startBtn.addEventListener('click', function() {
            startScanner();
        });
        
        stopBtn.addEventListener('click', function() {
            stopScanner();
        });
        
        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: scannerContainer,
                    constraints: {
                        width: 480,
                        height: 320,
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader"]
                },
                locate: true
            }, function(err) {
                if (err) {
                    console.error(err);
                    noCameraWarning.classList.remove('d-none');
                    return;
                }
                
                Quagga.start();
                scannerActive = true;
                startBtn.classList.add('d-none');
                stopBtn.classList.remove('d-none');
                resultadoDiv.style.display = 'none';
                errorMessage.classList.add('d-none');
            });
            
            Quagga.onDetected(function(result) {
                const code = result.codeResult.code;
                stopScanner();
                
                // Enviar código al servidor
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'codigo=' + encodeURIComponent(code)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        errorMessage.textContent = data.error;
                        errorMessage.classList.remove('d-none');
                    } else {
                        nombrePersona.textContent = 'Nombre: ' + data.persona.nombrecompletoper;
                        documentPersona.textContent = 'Documento: ' + data.persona.numerodoc;
                        tipoRegistro.textContent = 'Registro de ' + data.tipo.toUpperCase();
                        tipoRegistro.className = 'fw-bold text-' + (data.tipo === 'entrada' ? 'success' : 'danger');
                        resultadoDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMessage.textContent = 'Error al procesar el código';
                    errorMessage.classList.remove('d-none');
                });
            });
        }
        
        function stopScanner() {
            if (scannerActive) {
                Quagga.stop();
                scannerActive = false;
                startBtn.classList.remove('d-none');
                stopBtn.classList.add('d-none');
            }
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>