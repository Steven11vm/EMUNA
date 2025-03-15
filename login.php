<?php
session_start();

$users = [
    "SANDRA MENDOZA" => ["id" => 1, "password" => "123", "role" => "NUNCA TE RINDAS"],
];

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $error = "Por favor, ingresa tanto el usuario como la contraseña.";
    } elseif (isset($users[$username]) && $users[$username]["password"] === $password) {
        $_SESSION["username"] = $username;
        $_SESSION["role"] = $users[$username]["role"];
        header("Location: Index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMUNA - Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: url('ruta-a-tu-gif-de-jeringas.gif') center/cover fixed;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            z-index: 1;
        }

        .header {
            position: relative;
            z-index: 2;
            margin-bottom: 30px;
            text-align: center;
        }

        .header img {
            max-width: 200px;
            height: auto;
        }

        .modal {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        h2 {
            color: #ff69b4;
            margin-bottom: 30px;
            font-size: 28px;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
        }

        .error-message {
            color: #dc3545;
            background: rgba(255, 105, 180, 0.1);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            animation: shake 0.5s ease-in-out;
            border: 1px solid rgba(255, 105, 180, 0.2);
        }

        .input-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ff69b4;
            font-weight: 500;
            font-size: 16px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 2px solid rgba(255, 105, 180, 0.3);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        input:focus {
            border-color: #ff69b4;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.2);
        }

        button {
            background: linear-gradient(45deg, #ff69b4, #ff8dc7);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button:hover {
            background: linear-gradient(45deg, #ff8dc7, #ff69b4);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 105, 180, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        @media (max-width: 480px) {
            .modal {
                padding: 30px;
                margin: 10px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="header animate__animated animate__fadeIn">
        <img src="ruta-a-tu-logo.png" alt="EMUNA">
    </div>
    
    <div class="modal animate__animated animate__fadeIn">
        <form method="POST" action="">
            <h2 class="animate__animated animate__slideInDown">Iniciar sesión</h2>
            
            <?php if ($error): ?>
                <div class="error-message animate__animated animate__shakeX">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="input-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required 
                       class="animate__animated animate__fadeInUp">
            </div>
            
            <div class="input-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required 
                       class="animate__animated animate__fadeInUp">
            </div>
            
            <button type="submit" class="animate__animated animate__fadeInUp">
                Iniciar sesión
            </button>
        </form>
    </div>
</body>
</html>