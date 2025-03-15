<?php
session_start();

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
        // ... (resto de las páginas)
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMUNA - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

            <!-- Form Section -->
            <?php if($currentPage == 'clientes'): ?>
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
                                <button type="submit" class="btn btn-primary px-4">
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
        });
    </script>
</body>
</html> 