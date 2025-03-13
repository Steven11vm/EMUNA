<?php
require_once "./php/conexion.php";

$factura = isset($_GET['factura']) ? $_GET['factura'] : '';

// Primero, obtenemos el id del informe y el total
$consultaInforme = $conn->prepare("SELECT id, Valor_pago FROM informe WHERE Factura = ?");
$consultaInforme->bind_param("s", $factura);
$consultaInforme->execute();
$resultadoInforme = $consultaInforme->get_result();

if ($resultadoInforme->num_rows > 0) {
    $infoInforme = $resultadoInforme->fetch_assoc();
    $informe_id = $infoInforme['id'];
    $total = $infoInforme['Valor_pago'];

    // Ahora, consultamos los detalles
    $consultaDetalle = $conn->prepare("SELECT Area, Descripcion, Valor_unitario, Cantidad FROM detallitos WHERE id = ?");
    $consultaDetalle->bind_param("i", $informe_id);
    $consultaDetalle->execute();
    $resultadoDetalle = $consultaDetalle->get_result();

    if ($resultadoDetalle->num_rows > 0) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Factura</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4299e1;
            --secondary-color: #3182ce;
            --background-color: #f6f9fc;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-color) 0%, #eef2f7 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--text-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.98);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
        }

        .header h1 {
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .scrollable-table {
            margin-top: 20px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            font-weight: 500;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .total-section {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 16px;
            color: white;
            text-align: right;
            box-shadow: 0 8px 16px rgba(66, 153, 225, 0.2);
        }

        .total-label {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .total-value {
            font-size: 1.3rem;
            font-weight: 600;
            margin-left: 15px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .scrollable-table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detalles de la Factura: <?php echo htmlspecialchars($factura); ?></h1>
        </div>

        <div class="scrollable-table">
            <table>
                <thead>
                    <tr>
                        <th>Área</th>
                        <th>Descripción</th>
                        <th>Valor Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($fila = $resultadoDetalle->fetch_assoc()) {
                        $subtotal = $fila['Valor_unitario'] * $fila['Cantidad'];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($fila['Area']) . "</td>";
                        echo "<td>" . htmlspecialchars($fila['Descripcion']) . "</td>";
                        echo "<td>$" . number_format($fila['Valor_unitario'], 2, '.', ',') . "</td>";
                        echo "<td>" . htmlspecialchars($fila['Cantidad']) . "</td>";
                        echo "<td>$" . number_format($subtotal, 2, '.', ',') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <span class="total-label">Total Factura:</span>
            <span class="total-value">$<?php echo number_format($total, 2, '.', ','); ?></span>
        </div>
    </div>
</body>
</html>
<?php
    } else {
        echo "<div class='container'><p>No hay detalles para el pago proporcionada.</p></div>";
    }
    $consultaDetalle->close();
} else {
    echo "<div class='container'><p>No se encontró la factura proporcionada.</p></div>";
}

$consultaInforme->close();
$conn->close();
?>