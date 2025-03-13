<?php
session_start();

// Verificación de sesión
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Incluir archivo de conexión
require_once "./php/conexion.php";

// Funciones de utilidad
function uploadFile($file) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Generar nombre único para el archivo
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nombre_archivo = uniqid('doc_') . '.' . $extension;
        $ruta_destino = './uploads/' . $nombre_archivo;
        
        // Verificar y crear directorio si no existe
        if (!is_dir('./uploads/')) {
            mkdir('./uploads/', 0777, true);
        }
        
        // Mover el archivo
        if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
            return 'uploads/' . $nombre_archivo; // Retorna la ruta relativa para la BD
        }
    }
    return null;
}

function decompressZIP($zipPath) {
    $extractPath = './uploads/decompressed/';
    if (!is_dir($extractPath)) {
        mkdir($extractPath, 0777, true);
    }

    $zip = new ZipArchive;
    if ($zip->open($zipPath) === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
        return $extractPath;
    }
    return false;
}

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formData = [
        'nit' => $_POST["nit"],
        'fecha' => $_POST["fecha"],
        'nombre_proveedor' => $_POST["nombre_proveedor"],
        'factura' => $_POST["factura"],
        'valor_pago' => $_POST["valor_pago"],
        'prioridad' => $_POST["prioridad"],
        'aprobacion' => isset($_POST["aprobacion"]) ? 1 : 0
    ];
    $url_pdf = null;
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $url_pdf = uploadFile($_FILES['pdf']);
    }


    if (!empty($formData['nit']) && !empty($formData['fecha']) && !empty($formData['nombre_proveedor']) && 
    !empty($formData['factura']) && !empty($formData['valor_pago']) && !empty($formData['prioridad'])) {
        // Escapar los datos
        foreach ($formData as $key => $value) {
            $formData[$key] = $conn->real_escape_string($value);
        }

        $sql = "INSERT INTO informe (Nit, fecha, Nombre_proveedor, Factura, Valor_pago, url_pdf, Prioridad, Aprobacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        Nit = VALUES(Nit), 
        fecha = VALUES(fecha), 
        Nombre_proveedor = VALUES(Nombre_proveedor), 
        Factura = VALUES(Factura), 
        Valor_pago = VALUES(Valor_pago), 
        url_pdf = COALESCE(VALUES(url_pdf), url_pdf), 
        Prioridad = VALUES(Prioridad), 
        Aprobacion = VALUES(Aprobacion)";
   if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sssssssi", 
        $formData['nit'],
        $formData['fecha'],
        $formData['nombre_proveedor'],
        $formData['factura'],
        $formData['valor_pago'],
        $url_pdf,
        $formData['prioridad'],
        $formData['aprobacion']
    );
            if ($stmt->execute()) {
                // Procesar detalles del pago
                $detalles = json_decode($_POST['detalles'], true);
                
                if ($detalles && is_array($detalles)) {
                    $stmt_detalle = $conn->prepare("INSERT INTO detallitos (Area, Descripcion, Valor_unitario, Cantidad) VALUES (?, ?, ?, ?)");
                    
                    foreach ($detalles as $detalle) {
                        $stmt_detalle->bind_param("ssdd", 
                            $detalle['area'], 
                            $detalle['descripcion'], 
                            $detalle['valor_unitario'], 
                            $detalle['cantidad']
                        );
                        $stmt_detalle->execute();
                    }
                    
                    $stmt_detalle->close();
                }

                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Éxito!",
                        text: "Datos subidos y actualizados correctamente.",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "subir.php";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Error al subir o actualizar los datos: ' . $stmt->error . '",
                        confirmButtonText: "OK"
                    });
                </script>';
            }
            $stmt->close();
        }
    } else {
        echo '<script>
            Swal.fire({
                icon: "warning",
                title: "Campos incompletos",
                text: "Por favor, complete todos los campos obligatorios.",
                confirmButtonText: "OK"
            });
        </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Documentos</title>
    <link rel="icon" type="image/x-icon" href="./imagenes/logosas.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #0056b3;
            --secondary-color: #1a3a5f;
            --accent-color: #e74c3c;
            --background-color: #f5f7fa;
            --text-color: #2c3e50;
            --border-radius: 10px;
            --box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: linear-gradient(135deg, var(--secondary-color), #0a2540);
            color: white;
            padding: 1.2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }

        .user-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            transition: var(--transition);
        }

        .user-info span:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .user-info a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: rgba(231, 76, 60, 0.8);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            transition: var(--transition);
        }

        .user-info a:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }

        h1 {
            color: var(--secondary-color);
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2.5rem;
            position: relative;
            padding-bottom: 0.8rem;
        }

        h1:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }

        .form-container {
            background-color: white;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .form-container:hover {
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.7rem;
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 1.05rem;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-size: 1rem;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15);
        }

        .form-control[type="file"] {
            padding: 0.7rem;
            border: 2px dashed #ddd;
        }

        .btn {
            background: linear-gradient(135deg, var(--primary-color), #0078e7);
            color: white;
            padding: 0.9rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            background: linear-gradient(135deg, #0069d9, #0056b3);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            transition: var(--transition);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 2.5rem;
            border: none;
            width: 90%;
            max-width: 700px;
            border-radius: var(--border-radius);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }

        .close:hover,
        .close:focus {
            color: var(--accent-color);
            text-decoration: none;
        }

        .modal h2 {
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f0f0f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        th, td {
            border: 1px solid #eee;
            padding: 1rem;
            text-align: left;
        }

        th {
            background: linear-gradient(135deg, var(--primary-color), #0078e7);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f0f7ff;
        }

        .delete-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .delete-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 1rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }

            .modal-content {
                width: 95%;
                padding: 1.5rem;
                margin: 10% auto;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .user-info {
                flex-direction: column;
                width: 100%;
            }

            .user-info a, .user-info span {
                width: 100%;
                justify-content: center;
            }
        }

        /* Estilos para el selector de fecha */
        .ui-datepicker {
            padding: 1rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
        }

        .ui-datepicker .ui-datepicker-header {
            background: var(--primary-color);
            color: white;
            border-radius: 8px 8px 0 0;
            border: none;
        }

        .ui-datepicker th {
            color: var(--secondary-color);
            font-weight: 600;
        }

        .ui-datepicker .ui-state-default {
            background: #f5f5f5;
            border: 1px solid #ddd;
            color: var(--text-color);
            text-align: center;
            border-radius: 4px;
        }

        .ui-datepicker .ui-state-default:hover {
            background: #e9ecef;
        }

        .ui-datepicker .ui-state-active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Estilos para SweetAlert2 */
        .swal2-popup {
            border-radius: var(--border-radius);
            padding: 2rem;
        }

        .swal2-title {
            color: var(--secondary-color);
        }

        .swal2-confirm {
            background: var(--primary-color) !important;
            border-radius: 6px !important;
            padding: 0.8rem 1.5rem !important;
        }

        .swal2-cancel {
            background: #6c757d !important;
            border-radius: 6px !important;
            padding: 0.8rem 1.5rem !important;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h2>Sistema de Gestión de Documentos</h2>
                <div class="user-info">
                    <span><i class="fas fa-user"></i> <?= $_SESSION["username"] ?> (<?= $_SESSION["role"] ?>)</span>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <h1><i class="fas fa-file-upload"></i> Subir y Actualizar Datos</h1>

        <div class="form-container">
            <form action="subir.php" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nit"><i class="fas fa-id-card"></i> NIT</label>
                        <input type="text" name="nit" id="nit" class="form-control" required placeholder="Ingrese el NIT">
                    </div>
                    <div class="form-group">
                        <label for="fecha"><i class="fas fa-calendar"></i> Fecha</label>
                        <input type="text" name="fecha" id="fecha" class="form-control" placeholder="AAAA-MM-DD" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre_proveedor"><i class="fas fa-building"></i> Nombre Proveedor</label>
                        <input type="text" name="nombre_proveedor" id="nombre_proveedor" class="form-control" required placeholder="Nombre del proveedor">
                    </div>
                    <div class="form-group">
                        <label for="factura"><i class="fas fa-file-invoice"></i> Número de Factura</label>
                        <input type="text" name="factura" id="factura" class="form-control" required placeholder="Número de factura">
                    </div>
                    <div class="form-group">
                        <label for="valor_pago"><i class="fas fa-dollar-sign"></i> Valor de Pago</label>
                        <input type="number" name="valor_pago" id="valor_pago" class="form-control" step="0.01" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="prioridad"><i class="fas fa-flag"></i> Prioridad</label>
                        <select name="prioridad" id="prioridad" class="form-control" required>
                            <option value="" disabled selected>Selecciona una prioridad</option>
                            <option value="URGENTE">URGENTE</option>
                            <option value="MEDIA">MEDIA</option>
                            <option value="BAJA">BAJA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="detalle_pago"><i class="fas fa-info-circle"></i> Detalle del pago</label>
                        <button type="button" id="openModal" class="btn btn-block">Agregar detalles</button>
                        <input type="hidden" name="detalles" id="detalles">
                    </div>
                    <div class="form-group custom-file-upload">
                        <label for="pdf">
                            <i class="fas fa-file-pdf"></i> Documento PDF <span class="text-muted"></span>
                        </label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="pdf" id="pdf" class="form-control" accept="application/pdf,.zip">
                            <span class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </span>
                            <span class="check-icon">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <span class="file-name">Seleccionar archivo...</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" value="Guardar Documento" class="btn btn-block">
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para detalles del pago -->
    <div id="detallesModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Detalles del Pago</h2>
            <form id="detallesForm">
                <div class="form-group">
                    <label for="area">Área</label>
                    <input type="text" id="area" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <input type="text" id="descripcion" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="valor_unitario">Valor Unitario</label>
                    <input type="number" id="valor_unitario" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" class="form-control" step="0.01" required>
                </div>
                <button type="button" id="agregarDetalle" class="btn btn-block">Agregar Detalle</button>
            </form>
            <table id="detallesTable">
                <thead>
                    <tr>
                        <th>Área</th>
                        <th>Descripción</th>
                        <th>Valor Unitario</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

<style>
    .custom-file-upload {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .custom-file-upload label {
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .file-upload-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 15px;
        background: #f9f9f9;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        overflow: hidden;
    }

    .file-upload-wrapper:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .file-upload-wrapper input {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .upload-icon {
        font-size: 24px;
        color: #6c757d;
        margin-right: 15px;
        transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
    }

    .check-icon {
        font-size: 24px;
        color: #28a745;
        position: absolute;
        right: 15px;
        opacity: 0;
        transform: scale(0);
        transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
    }

    .file-name {
        flex: 1;
        text-align: center;
        color: #495057;
        font-size: 15px;
        transition: color 0.3s ease-in-out;
    }

    /* Animación cuando se selecciona un archivo */
    .file-upload-wrapper.file-selected .upload-icon {
        transform: scale(0);
        opacity: 0;
    }

    .file-upload-wrapper.file-selected .check-icon {
        transform: scale(1);
        opacity: 1;
    }

    .file-upload-wrapper.file-selected {
        border-color: #28a745;
        background: #e9f8ec;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
    }

    .file-upload-wrapper.file-selected .file-name {
        color: #28a745;
        font-weight: bold;
    }
</style>

    <script>
        $(function() {
            $("#fecha").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: "2000:2025"
            });

            // Modal
            var modal = document.getElementById("detallesModal");
            var btn = document.getElementById("openModal");
            var span = document.getElementsByClassName("close")[0];
            var detalles = [];

            btn.onclick = function() {
                modal.style.display = "block";
                document.body.style.overflow = "hidden"; // Prevenir scroll
            }

            span.onclick = function() {
                modal.style.display = "none";
                document.body.style.overflow = "auto"; // Restaurar scroll
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                    document.body.style.overflow = "auto"; // Restaurar scroll
                }
            }

            // Agregar detalle
            $("#agregarDetalle").click(function() {
                var area = $("#area").val();
                var descripcion = $("#descripcion").val();
                var valor_unitario = $("#valor_unitario").val();
                var cantidad = $("#cantidad").val();

                if(area && descripcion && valor_unitario && cantidad) {
                    detalles.push({area, descripcion, valor_unitario, cantidad});
                    actualizarTablaDetalles();
                    $("#detallesForm")[0].reset();
                    
                    // Animación de éxito
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Detalle agregado',
                        showConfirmButton: false,
                        timer: 1500,
                        toast: true
                    });
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "Campos incompletos",
                        text: "Por favor, complete todos los campos del detalle.",
                        confirmButtonText: "OK"
                    });
                }
            });

            function actualizarTablaDetalles() {
                var tbody = $("#detallesTable tbody");
                tbody.empty();
                detalles.forEach(function(detalle, index) {
                    tbody.append(`
                        <tr>
                            <td>${detalle.area}</td>
                            <td>${detalle.descripcion}</td>
                            <td>${detalle.valor_unitario}</td>
                            <td>${detalle.cantidad}</td>
                            <td>
                                <button class="delete-btn" data-index="${index}">Eliminar</button>
                            </td>
                        </tr>
                    `);
                });
                $("#detalles").val(JSON.stringify(detalles));
            }

            // Eliminar detalle
            $(document).on('click', '.delete-btn', function() {
                var index = $(this).data('index');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        detalles.splice(index, 1);
                        actualizarTablaDetalles();
                        Swal.fire(
                            'Eliminado',
                            'El detalle ha sido eliminado.',
                            'success'
                        );
                    }
                });
            });
            
            // Manejar el cambio de archivo
            document.getElementById("pdf").addEventListener("change", function (e) {
                let fileName = e.target.files[0] ? e.target.files[0].name : "Seleccionar archivo...";
                let wrapper = e.target.parentElement;
                let fileNameElement = wrapper.querySelector(".file-name");
                
                fileNameElement.textContent = fileName;
                wrapper.classList.add("file-selected");
            });
        });
    </script>
</body>
</html>