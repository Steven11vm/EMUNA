<?php
 require_once "session_check.php"; 
// Verificación de sesión
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Función para obtener el contenido según la página
function getPageContent($page) {
    $content = [
        'clientes' => [
            'title' => 'Registro de Clientes',
            'headers' => ['Nombre', 'Email', 'Teléfono', 'Última Visita', 'Estado', 'Acciones'],
            'data' => [
                ['María García', 'maria@email.com', '+1234567890', '2024-03-15', 'Activo'],
                ['Juan Pérez', 'juan@email.com', '+1234567891', '2024-03-10', 'Activo'],
                ['Ana Rodríguez', 'ana@email.com', '+1234567892', '2024-03-05', 'Activo']
            ]
        ],
        'citas' => [
            'title' => 'Gestión de Citas',
            'headers' => ['Paciente', 'Fecha', 'Hora', 'Tratamiento', 'Estado', 'Acciones'],
            'data' => [
                ['María García', '2024-03-20', '10:00', 'Sueroterapia', 'Pendiente'],
                ['Juan Pérez', '2024-03-21', '11:30', 'Facial', 'Confirmada'],
                ['Ana Rodríguez', '2024-03-22', '15:00', 'Sueroterapia', 'Pendiente']
            ]
        ],
        'tratamientos' => [
            'title' => 'Seguimiento de Tratamientos',
            'headers' => ['Paciente', 'Tratamiento', 'Inicio', 'Fin', 'Progreso', 'Acciones'],
            'data' => [
                ['María García', 'Sueroterapia', '2024-02-15', '2024-04-15', '50%'],
                ['Juan Pérez', 'Facial', '2024-03-01', '2024-05-01', '25%'],
                ['Ana Rodríguez', 'Sueroterapia', '2024-03-10', '2024-05-10', '15%']
            ]
        ],
        'estadisticas' => [
            'title' => 'Estadísticas',
            'headers' => ['Métrica', 'Valor', 'Cambio', 'Período', 'Tendencia', 'Acciones'],
            'data' => [
                ['Clientes Nuevos', '45', '+15%', 'Este mes', '↑'],
                ['Ingresos', '$5,234', '+22%', 'Este mes', '↑'],
                ['Tratamientos', '156', '+10%', 'Este mes', '↑']
            ]
        ],
        'recordatorios' => [
            'title' => 'Recordatorios',
            'headers' => ['Tipo', 'Paciente', 'Fecha', 'Mensaje', 'Estado', 'Acciones'],
            'data' => [
                ['Cita', 'María García', '2024-03-20', 'Recordatorio de cita', 'Pendiente'],
                ['Seguimiento', 'Juan Pérez', '2024-03-21', 'Seguimiento tratamiento', 'Enviado'],
                ['Cita', 'Ana Rodríguez', '2024-03-22', 'Recordatorio de cita', 'Pendiente']
            ]
        ],
        'compras' => [
            'title' => 'Gestión de Compras',
            'headers' => ['Producto', 'Proveedor', 'Cantidad', 'Precio', 'Fecha', 'Estado', 'Acciones'],
            'data' => [
                ['Suero Vitamina C', 'Laboratorios ABC', '50', '$1,500', '2024-03-10', 'Recibido'],
                ['Crema Facial', 'Cosméticos XYZ', '30', '$900', '2024-03-15', 'Pendiente'],
                ['Jeringas', 'Medical Supplies', '100', '$300', '2024-03-05', 'Recibido']
            ]
        ]
    ];

    return isset($content[$page]) ? $content[$page] : $content['clientes'];
}

$currentPage = isset($_GET['page']) ? $_GET['page'] : 'clientes';
$pageContent = getPageContent($currentPage);

// Guardar estado del menú
if (isset($_POST['menuState'])) {
    $_SESSION['menuCollapsed'] = $_POST['menuState'] === 'true';
    exit('OK');
}

$menuCollapsed = isset($_SESSION['menuCollapsed']) ? $_SESSION['menuCollapsed'] : false;

// Datos para gráficos (simulados)
$chartData = [
    'clientes' => [
        'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        'data' => [65, 78, 90, 105, 120, 145]
    ],
    'citas' => [
        'labels' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
        'data' => [12, 19, 15, 17, 14, 8, 2]
    ],
    'ingresos' => [
        'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        'data' => [3500, 4200, 5100, 4800, 5600, 6200]
    ],
    'tratamientos' => [
        'labels' => ['Sueroterapia', 'Facial', 'Botox', 'Masajes', 'Otros'],
        'data' => [45, 25, 15, 10, 5]
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMUNA - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #ff69b4;
            --primary-dark: #ff4bac;
            --secondary: #ff8dc7;
            --background: #fff5f9;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --header-height: 60px;
            --transition-speed: 0.3s;
        }

        body {
            min-height: 100vh;
            background-color: var(--background);
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Header styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1030;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: white;
            transition: width var(--transition-speed);
            z-index: 1020;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-toggle {
            position: absolute;
            right: 1px;
            top: 20px;
            width: 30px;
            height: 30px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: transform var(--transition-speed);
            z-index: 1;
        }

        .sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #666;
            text-decoration: none;
            transition: all var(--transition-speed);
            white-space: nowrap;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
            margin-right: 1rem;
        }

        .menu-item:hover {
            background: var(--background);
            color: var(--primary);
        }

        .menu-item.active {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
        }

        .sidebar.collapsed .menu-text {
            display: none;
        }

        /* Main content styles */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            transition: margin-left var(--transition-speed);
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Cards styles */
        .stats-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 15px;
            padding: 1.5rem;
            height: 100%;
            color: white;
            transition: transform var(--transition-speed);
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Chart styles */
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 1rem;
        }

        /* Form styles */
        .form-control {
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 0.75rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(255,105,180,0.25);
        }

        /* Table styles */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .table thead th {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
                width: var(--sidebar-width) !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .mobile-toggle {
                display: block !important;
            }
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 0.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="d-flex align-items-center">
            <button class="mobile-toggle me-2" id="mobile-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <a href="#" class="brand">
                <i class="fas fa-spa"></i>
                EMUNA
            </a>
        </div>
        <div class="user-info">
            <span>Bienvenido, <?= htmlspecialchars($_SESSION["username"]) ?></span>
            <?php if(isset($_SESSION["role"])): ?>
                <span class="role-badge"><?= htmlspecialchars($_SESSION["role"]) ?></span>
            <?php endif; ?>
            <a href="logout.php" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                Cerrar sesión
            </a>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar <?= $menuCollapsed ? 'collapsed' : '' ?>" id="sidebar">
        <div class="sidebar-toggle" id="sidebar-toggle">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="py-3">
            <a href="?page=clientes" class="menu-item <?= $currentPage == 'clientes' ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                <span class="menu-text">Registro de Clientes</span>
            </a>
            <a href="?page=citas" class="menu-item <?= $currentPage == 'citas' ? 'active' : '' ?>">
                <i class="fas fa-calendar"></i>
                <span class="menu-text">Gestión de Citas</span>
            </a>
            <a href="?page=tratamientos" class="menu-item <?= $currentPage == 'tratamientos' ? 'active' : '' ?>">
                <i class="fas fa-syringe"></i>
                <span class="menu-text">Tratamientos</span>
            </a>
            <a href="?page=estadisticas" class="menu-item <?= $currentPage == 'estadisticas' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                <span class="menu-text">Estadísticas</span>
            </a>
            <a href="?page=recordatorios" class="menu-item <?= $currentPage == 'recordatorios' ? 'active' : '' ?>">
                <i class="fas fa-bell"></i>
                <span class="menu-text">Recordatorios</span>
            </a>
            <a href="?page=compras" class="menu-item <?= $currentPage == 'compras' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i>
                <span class="menu-text">Gestión de Compras</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content <?= $menuCollapsed ? 'expanded' : '' ?>" id="main-content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-4"><?= htmlspecialchars($pageContent['title']) ?></h1>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-4">
                    <div class="stats-card">
                        <div class="stats-label">Total Clientes</div>
                        <div class="stats-number">1,234</div>
                        <div class="stats-change">
                            <i class="fas fa-arrow-up"></i> 15% este mes
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stats-card">
                        <div class="stats-label">Citas Programadas</div>
                        <div class="stats-number">42</div>
                        <div class="stats-change">Para esta semana</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stats-card">
                        <div class="stats-label">Tratamientos Activos</div>
                        <div class="stats-number">156</div>
                        <div class="stats-change">En curso</div>
                    </div>
                </div>
            </div>

            <?php if($currentPage == 'estadisticas'): ?>
            <!-- Gráficos para la página de estadísticas -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="content-card">
                        <div class="card-header">
                            <h5 class="mb-0">Evolución de Clientes</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="clientesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="content-card">
                        <div class="card-header">
                            <h5 class="mb-0">Citas por Día</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="citasChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="content-card">
                        <div class="card-header">
                            <h5 class="mb-0">Ingresos Mensuales</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="ingresosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="content-card">
                        <div class="card-header">
                            <h5 class="mb-0">Distribución de Tratamientos</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="tratamientosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($currentPage == 'compras'): ?>
            <!-- Formulario de Compras -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Nueva Compra</h5>
                </div>
                <div class="card-body">
                    <form id="compraForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Producto</label>
                                <input type="text" class="form-control" name="producto" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Proveedor</label>
                                <input type="text" class="form-control" name="proveedor" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cantidad</label>
                                <input type="number" class="form-control" name="cantidad" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Precio Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="precio" min="0" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fecha de Compra</label>
                                <input type="date" class="form-control" name="fecha" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea class="form-control" name="notas" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Registrar Compra
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if($currentPage == 'clientes'): ?>
            <!-- Formulario de Clientes -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Nuevo Cliente</h5>
                </div>
                <div class="card-body">
                    <form id="clientForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" name="fecha_nacimiento" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas Médicas</label>
                                <textarea class="form-control" name="notas" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                            <button type="submit" class="btn px-4" style="background-color: #e83e8c; color: white;">
    <i class="fas fa-save me-2"></i>Guardar Cliente
</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Table Section -->
            <div class="table-container">
                <div class="card-header">
                    <h5 class="mb-0"><?= htmlspecialchars($pageContent['title']) ?></h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <?php foreach($pageContent['headers'] as $header): ?>
                                    <th><?= htmlspecialchars($header) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pageContent['data'] as $row): ?>
                                <tr>
                                    <?php foreach($row as $cell): ?>
                                        <td><?= htmlspecialchars($cell) ?></td>
                                    <?php endforeach; ?>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileToggle = document.getElementById('mobile-toggle');

            // Función para guardar el estado del menú en el servidor
            function saveMenuState(collapsed) {
                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'menuState=' + collapsed
                });
            }

            // Función para colapsar/expandir el menú
            function toggleSidebar() {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                saveMenuState(isCollapsed);
            }

            // Función para mostrar/ocultar el menú en móviles
            function toggleMobileMenu() {
                sidebar.classList.toggle('show');
            }

            // Event listeners
            sidebarToggle.addEventListener('click', toggleSidebar);
            mobileToggle.addEventListener('click', toggleMobileMenu);

            // Cerrar menú móvil al hacer clic en un enlace
            document.querySelectorAll('.menu-item').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('show');
                    }
                });
            });

            // Manejar cambios de tamaño de ventana
            window.addEventListener('resize', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('show');
                }
            });

            // Inicializar gráficos si estamos en la página de estadísticas
            <?php if($currentPage == 'estadisticas'): ?>
            // Gráfico de Clientes
            const clientesCtx = document.getElementById('clientesChart').getContext('2d');
            new Chart(clientesCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartData['clientes']['labels']) ?>,
                    datasets: [{
                        label: 'Total de Clientes',
                        data: <?= json_encode($chartData['clientes']['data']) ?>,
                        backgroundColor: 'rgba(255, 105, 180, 0.2)',
                        borderColor: 'rgba(255, 105, 180, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Citas
            const citasCtx = document.getElementById('citasChart').getContext('2d');
            new Chart(citasCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($chartData['citas']['labels']) ?>,
                    datasets: [{
                        label: 'Citas por Día',
                        data: <?= json_encode($chartData['citas']['data']) ?>,
                        backgroundColor: 'rgba(255, 141, 199, 0.7)',
                        borderColor: 'rgba(255, 141, 199, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Ingresos
            const ingresosCtx = document.getElementById('ingresosChart').getContext('2d');
            new Chart(ingresosCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartData['ingresos']['labels']) ?>,
                    datasets: [{
                        label: 'Ingresos Mensuales ($)',
                        data: <?= json_encode($chartData['ingresos']['data']) ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Tratamientos
            const tratamientosCtx = document.getElementById('tratamientosChart').getContext('2d');
            new Chart(tratamientosCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($chartData['tratamientos']['labels']) ?>,
                    datasets: [{
                        label: 'Distribución de Tratamientos',
                        data: <?= json_encode($chartData['tratamientos']['data']) ?>,
                        backgroundColor: [
                            'rgba(255, 105, 180, 0.7)',
                            'rgba(255, 141, 199, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 105, 180, 1)',
                            'rgba(255, 141, 199, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>