<?php
require_once "conexion.php";

// Handle PDF upload
if(isset($_FILES['pdf_file']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $archivo = $_FILES['pdf_file'];
    
    // Validate file type
    if($archivo['type'] !== 'application/pdf') {
        die("Error: Solo se permiten archivos PDF");
    }
    
    // Generate unique filename
    $nombre_archivo = uniqid('pdf_') . '.pdf';
    $ruta_destino = __DIR__ . '/../uploads/' . $nombre_archivo;
    $ruta_bd = 'uploads/' . $nombre_archivo;
    
    if(move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        $sql = "UPDATE informe SET url_pdf = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $ruta_bd, $id);
        
        if($stmt->execute() && $stmt->affected_rows > 0) {
            echo "PDF guardado correctamente";
        } else {
            unlink($ruta_destino); // Delete uploaded file if DB update fails
            die("Error: No se pudo actualizar la base de datos");
        }
    } else {
        die("Error: No se pudo subir el archivo");
    }
}

// Handle PDF display
elseif(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $sql = "SELECT url_pdf FROM informe WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($ruta_pdf);
        $stmt->fetch();
        
        if(empty($ruta_pdf)) {
            die("Error: No hay PDF asociado a este registro");
        }
        
        $ruta_completa = __DIR__ . '/../' . $ruta_pdf;
        
        if(!file_exists($ruta_completa)) {
            die("Error: El archivo no existe en: " . $ruta_completa);
        }
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($ruta_pdf) . '"');
        header('Content-Length: ' . filesize($ruta_completa));
        header('Cache-Control: private, max-age=0, must-revalidate');
        
        readfile($ruta_completa);
        exit();
        
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Error: Solicitud inválida");
}
?>