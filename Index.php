<?php require_once "session_check.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMUNA - Sistema de Gestión de Sueroterapia y Cuidado Facial</title>
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #fff5f7;
            color: #4a4a4a;
            line-height: 1.6;
        }
        
        /* Contenedor principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Estilos del encabezado */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
        }
        
        .logo-icon {
            font-size: 24px;
            margin-right: 10px;
            color: #f472b6;
        }
        
        .logo h1 {
            font-size: 28px;
            font-weight: 700;
            color: #f472b6;
            letter-spacing: 1px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(244, 114, 182, 0.2);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 114, 182, 0.3);
        }
        
        /* Estilos para el usuario logueado */
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info span {
            font-weight: 500;
            color: #4a4a4a;
        }
        
        .role-badge {
            background-color: #f472b6;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .logout-link {
            background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(244, 114, 182, 0.2);
        }
        
        .logout-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(244, 114, 182, 0.3);
        }
        
        /* Estilos de la sección hero */
        .hero {
            text-align: center;
            padding: 60px 0;
        }
        
        .hero h2 {
            font-size: 42px;
            font-weight: 700;
            color: #f472b6;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
            color: #6b7280;
        }
        
        /* Estilos de características */
        .features {
            padding: 40px 0 80px;
        }
        
        .features h3 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #4a4a4a;
        }
        
        .features > p {
            font-size: 18px;
            margin-bottom: 40px;
            color: #6b7280;
        }
        
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .feature-item {
            background-color: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(244, 114, 182, 0.08);
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-start;
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(244, 114, 182, 0.12);
        }
        
        .feature-icon {
            font-size: 30px;
            margin-right: 20px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fce7f3 0%, #f9a8d4 100%);
            border-radius: 12px;
            color: #f472b6;
        }
        
        .feature-content h4 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #f472b6;
        }
        
        .feature-content p {
            color: #6b7280;
        }
        
        /* Botones principales */
        .main-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 60px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
            color: white;
            border: none;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(244, 114, 182, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 114, 182, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #f472b6;
            border: 2px solid #f472b6;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(244, 114, 182, 0.05);
            transform: translateY(-2px);
        }
        
        /* Footer */
        footer {
            background-color: white;
            text-align: center;
            padding: 40px 0;
            color: #6b7280;
            border-top: 1px solid #fce7f3;
        }
        
        .footer-logo {
            color: #f472b6;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        /* Elementos adicionales para un aspecto más moderno */
        .decorative-shape {
            position: absolute;
            z-index: -1;
            opacity: 0.5;
        }
        
        .shape-1 {
            top: 100px;
            left: 10%;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #fce7f3 0%, #f9a8d4 100%);
            border-radius: 50%;
            filter: blur(90px);
        }
        
        .shape-2 {
            bottom: 100px;
            right: 10%;
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #ddd6fe 0%, #a78bfa 100%);
            border-radius: 50%;
            filter: blur(80px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h2 {
                font-size: 32px;
            }
            
            .features h3 {
                font-size: 26px;
            }
            
            .feature-list {
                grid-template-columns: 1fr;
            }
            
            .main-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .user-info {
                flex-direction: column;
                align-items: flex-end;
                gap: 8px;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="decorative-shape shape-1"></div>
    <div class="decorative-shape shape-2"></div>

    <div class="container">
        <header>
            <div class="logo">
                <span class="logo-icon">✨</span>
                <h1>EMUNA</h1>
            </div>
            <?php if(isset($_SESSION["username"])): ?>
                <div class="user-info">
                    <span>Bienvenido, <?= $_SESSION["username"] ?></span>
                    <?php if(isset($_SESSION["role"])): ?>
                        <span class="role-badge"><?= $_SESSION["role"] ?></span>
                    <?php endif; ?>
                    <a href="logout.php" class="logout-link">Cerrar sesión</a>
                </div>
            <?php else: ?>
                <button class="btn-login" onclick="window.location.href='login.php'">Iniciar Sesión</button>
            <?php endif; ?>
        </header>

        <main>
            <section class="hero">
                <h2>Registro de Clientes</h2>
                <p>Sistema de gestión para tu negocio de sueroterapia y cuidado facial. Registra, organiza y da seguimiento a tus clientes de manera eficiente.</p>
                
                <div class="main-buttons">
                    <a href="Registro.php"><button class="btn-primary">Comenzar Ahora</button></a>
                   
                </div>
            </section>

            <section class="features">
                <h3>Gestiona tu negocio con elegancia</h3>
                <p>EMUNA te permite llevar un control detallado de tus clientes, tratamientos, citas y seguimientos para brindar un servicio excepcional.</p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">👤</div>
                        <div class="feature-content">
                            <h4>Registro de Clientes</h4>
                            <p>Almacena información detallada de tus clientes y su historial completo de tratamientos y visitas.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">📅</div>
                        <div class="feature-content">
                            <h4>Gestión de Citas</h4>
                            <p>Organiza tu agenda, configura recordatorios y evita solapamientos de horarios fácilmente.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">💉</div>
                        <div class="feature-content">
                            <h4>Seguimiento de Tratamientos</h4>
                            <p>Mantén un registro detallado de los tratamientos de sueroterapia y su evolución en el tiempo.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">📊</div>
                        <div class="feature-content">
                            <h4>Estadísticas y Reportes</h4>
                            <p>Visualiza datos importantes de tu negocio con informes claros y gráficos intuitivos.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">🔔</div>
                        <div class="feature-content">
                            <h4>Recordatorios Automáticos</h4>
                            <p>Envía notificaciones a tus clientes para próximas citas y seguimientos.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="footer-logo">EMUNA</div>
            <p>© 2025 EMUNA - Sistema de Gestión para Sueroterapia y Cuidado Facial,Sandra Mendoza</p>
        </footer>
    </div>
</body>
</html>