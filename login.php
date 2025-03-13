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

        if ($username === "Sky") {
            header("Location: subir.php");
        } else {
            header("Location: Index.php");
        }
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
    <title>Iniciar Sesion</title>
    <link rel="icon" type="image/x-icon" href="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
            text-align: center;
        }

        .header img {
            max-width: 250px;
            height: auto;
        }

        .modal {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.8s ease-out;
        }

        h2 {
            color: #003B7A;
            margin-bottom: 30px;
            font-size: 28px;
            text-align: center;
            font-weight: 600;
        }

        .error-message {
            color: #dc3545;
            background: #f8d7da;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            animation: shake 0.5s ease-in-out;
        }

        .input-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #003B7A;
            font-weight: 500;
            font-size: 16px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #003B7A;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 59, 122, 0.1);
        }

        button {
            background: #003B7A;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #002857;
            transform: translateY(-2px);
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
    <div class="header">
        <img src="" alt="EMUNA">
    </div>
    
    <div class="modal animate__animated animate__fadeIn">
        <form method="POST" action="">
            <h2 class="animate__animated animate__slideInDown">Iniciar sesión</h2>
            
            <?php if ($error): ?>
                <div class="error-message animate__animated animate__shakeX">
                    <?php echo $error; ?>
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