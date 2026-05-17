<?php
session_start();
include '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo   = trim($_POST['correo']);
    $password = $_POST['password'];

    if (empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, correo, password FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario   = $resultado->fetch_assoc();
        

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];
            header('Location: ../pages/perfil.php');
            exit();
        } else {
            $error = "Correo o contraseña incorrectos";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="card">
    <h2>Iniciar sesión</h2>
    <p class="subtitle">Inicia sesion para continuar</p>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
        <label>Correo</label>
        <input type="email" name="correo" required>
        </div>
        <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" required>
        </div>

        <button type="submit">Iniciar sesión</button>

    </form>

    <div class="links">
        <a href="registro.php">¿No tienes cuenta? Regístrate</a>
    </div>
    </div>
</body>
</html> 