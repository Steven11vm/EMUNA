<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMUNA - Registro de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff69b4;
            --secondary-color: #ff8dc7;
            --background-color: #fff5f9;
            --text-color: #4a4a4a;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
        }

        .navbar {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            padding: 1rem 2rem;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            background: white;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .stats-card {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #eee;
            padding: 0.75rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 180, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 105, 180, 0.3);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }

        .badge-custom {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: white;
        }

        .sidebar {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            height: calc(100vh - 2rem);
            margin: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-link {
            color: var(--text-color);
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: block;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background: var(--background-color);
            color: var(--primary-color);
        }

        .sidebar-link.active {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-spa me-2"></i>EMUNA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-bell me-1"></i> Notificaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user me-1"></i> Perfil</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="sidebar">
                    <h5 class="mb-4">Menú Principal</h5>
                    <a href="#" class="sidebar-link active mb-2">
                        <i class="fas fa-users me-2"></i> Registro de Clientes
                    </a>
                    <a href="#" class="sidebar-link mb-2">
                        <i class="fas fa-calendar me-2"></i> Gestión de Citas
                    </a>
                    <a href="#" class="sidebar-link mb-2">
                        <i class="fas fa-syringe me-2"></i> Tratamientos
                    </a>
                    <a href="#" class="sidebar-link mb-2">
                        <i class="fas fa-chart-bar me-2"></i> Estadísticas
                    </a>
                    <a href="#" class="sidebar-link mb-2">
                        <i class="fas fa-bell me-2"></i> Recordatorios
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="mb-4">Registro de Clientes</h2>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="col-md-4 mb-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Clientes</h5>
                                <h2>1,234</h2>
                                <p class="mb-0"><i class="fas fa-arrow-up"></i> 15% este mes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Citas Programadas</h5>
                                <h2>42</h2>
                                <p class="mb-0">Para esta semana</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card stats-card">
                            <div class="card-body">
                                <h5 class="card-title">Tratamientos Activos</h5>
                                <h2>156</h2>
                                <p class="mb-0">En curso</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registro Form -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Nuevo Cliente</h5>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Notas Médicas</label>
                                    <textarea class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Guardar Cliente
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Client Table -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Clientes Recientes</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Última Visita</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>María García</td>
                                        <td>maria@email.com</td>
                                        <td>+1234567890</td>
                                        <td>2024-03-15</td>
                                        <td><span class="badge badge-custom">Activo</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Más filas aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>