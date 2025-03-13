<?php
// api.php
require_once 'conexion.php';
header('Content-Type: application/json');

function obtenerPagos($conn) {
    $fecha_inicial = isset($_GET["inicio"]) ? $_GET["inicio"] : "";
    $fecha_final = isset($_GET["final"]) ? $_GET["final"] : "";
    $estado = isset($_GET["estado"]) ? $_GET["estado"] : "";
    $doc_num = isset($_GET["doc_num"]) ? $_GET["doc_num"] : "";

    $where = " WHERE 1=1";
    if (!empty($fecha_inicial) && !empty($fecha_final)) {
        $where .= " AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'";
    }
    if (!empty($estado)) {
        $where .= " AND Prioridad = '$estado'";
    }
    if (!empty($doc_num)) {
        $where .= " AND Factura = '$doc_num'";
    }

    $sql = "SELECT * FROM pagos" . $where;
    $result = $conn->query($sql);
    
    $pagos = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $pagos[] = $row;
        }
    }
    
    return json_encode($pagos);
}

function actualizarPagos($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $facturas = $data['facturas'] ?? [];
    
    if (empty($facturas)) {
        return json_encode(['success' => false, 'message' => 'No se seleccionaron facturas']);
    }

    $success = true;
    $errors = [];
    
    foreach ($facturas as $factura) {
        $factura = $conn->real_escape_string($factura);
        $sql = "UPDATE pagos SET Nombre_aprobacion = 'aprobado' WHERE Factura = '$factura'";
        
        if (!$conn->query($sql)) {
            $success = false;
            $errors[] = "Error actualizando factura $factura: " . $conn->error;
        }
    }
    
    return json_encode([
        'success' => $success,
        'message' => $success ? 'Actualización exitosa' : 'Errores en la actualización',
        'errors' => $errors
    ]);
}

// Manejo de las peticiones
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo obtenerPagos($conn);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo actualizarPagos($conn);
}

$conn->close();
?>