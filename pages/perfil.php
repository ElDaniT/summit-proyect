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

// Obtener datos actuales del usuario
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre           = trim($_POST['nombre']);
    $correo           = trim($_POST['correo']);
    $telefono         = trim($_POST['telefono']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    if (empty($nombre) || empty($correo)) {
        $error = "El nombre y correo son obligatorios";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo no es válido";

    } else {
        // Verificar que el correo no lo use otro usuario
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
        $stmt->bind_param("si", $correo, $_SESSION['id']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "El correo ya está en uso por otro usuario";
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, telefono = ?, fecha_nacimiento = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nombre, $correo, $telefono, $fecha_nacimiento, $_SESSION['id']);

            if ($stmt->execute()) {
                $_SESSION['nombre'] = $nombre;
                $_SESSION['correo'] = $correo;
                $exito = "Perfil actualizado correctamente";
                $usuario['nombre'] = $nombre;
                $usuario['correo'] = $correo;
                $usuario['telefono'] = $telefono;
                $usuario['fecha_nacimiento'] = $fecha_nacimiento;
            } else {
                $error = "Error al actualizar el perfil";
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
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head> 
<body>

    <div class="card">
    <h2>Mi Perfil</h2>
    <p class="subtitle">Bienvenido, <?= $usuario['nombre'] ?></p>

    <?php if ($error): ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($exito): ?>
        <div class="mensaje-exito"><?= $exito ?></div>
    <?php endif; ?>


        <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>
        </div>
        <div class="form-group">
        <label>Correo</label>
        <input type="email" name="correo" value="<?= $usuario['correo'] ?>" required>
        </div>
    <form method="POST" action="">
        <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?= $usuario['telefono'] ?>">
        </div>
        <div class="form-group">
        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" value="<?= $usuario['fecha_nacimiento'] ?>">
        </div>

        <button type="submit">Actualizar perfil</button>

    </form>

    <div class="links">
    <a href="cambiar_password.php">Cambiar contraseña</a> <span>|</span> 
    <a href="../auth/logout.php">Cerrar sesión</a>
    </div>
    </div>

</body>
</html>