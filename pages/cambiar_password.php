<?php
session_start();
include '../config/db.php';

// Proteger la pagina privada que no esta iniciada
if (!isset($_SESSION['id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_actual   = $_POST['password_actual'];
    $password_nueva    = $_POST['password_nueva'];
    $password_confirm  = $_POST['password_confirm'];

    if (empty($password_actual) || empty($password_nueva) || empty($password_confirm)) {
        $error = "Todos los campos son obligatorios";

    } elseif ($password_nueva !== $password_confirm) {
        $error = "La nueva contraseña y la confirmación no coinciden";

    } elseif (strlen($password_nueva) < 6) {
        $error = "La nueva contraseña debe tener al menos 6 caracteres";

    } else {
        // Obtener la contraseña actual de la db    
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['id']);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!password_verify($password_actual, $usuario['password'])) {
            $error = "La contraseña actual es incorrecta";
        } else {
            // Guardar nueva contraseña con hash
            $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $nuevo_hash, $_SESSION['id']);

            if ($stmt->execute()) {
                $exito = "Contraseña actualizada correctamente";
            } else {
                $error = "Error al actualizar la contraseña";
            }
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
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="card">
    <h1 class="app-title">Ecuador Summit</h1>
    <h2>Cambiar Contraseña</h2>

    <?php if ($error): ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="mensaje-exito"><?= $exito ?></div>
    <?php endif; ?>

    <form method="POST" action="">
          <div class="form-group">
        <label>Contraseña actual</label>
        <input type="password" name="password_actual" required>
        </div>
        <div class="form-group">
        <label>Nueva contraseña</label>
        <input type="password" name="password_nueva" required>
        </div>
        <div class="form-group">
        <label>Confirmar nueva contraseña</label>
        <input type="password" name="password_confirm" required>
        </div>

        <button type="submit">Cambiar contraseña</button>

    </form>

    <div class="links">
    <a href="perfil.php">Volver al perfil</a> <span>|</span>
    <a href="../auth/logout.php">Cerrar sesión</a>
    </div>
    </div>

</body>
</html>