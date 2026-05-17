<?php
session_start();
include '../config/db.php';

$error = '';
$exito = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
   $cedula = trim($_POST['cedula']);
   $nombre = trim($_POST['nombre']);
   $correo = trim($_POST['correo']);
   $password = trim($_POST['password']);
   $telefono = trim($_POST['telefono']);
   $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
   

//revisa que no esten datos vacios
   if (empty($cedula) || empty($nombre) || empty($correo) || empty($password)) {
        $error = "Los campos cédula, nombre, correo y contraseña son obligatorios";
// Validar que el formato del correo este bien
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo no es válido";
    
    } else {
        // Verificar si el correo ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "El correo ya está registrado";
        } else {
            // Guardar la contraseña con hash
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO usuarios 
                (cedula, nombre, correo, password, telefono, fecha_nacimiento) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $cedula, $nombre, $correo, $hash, $telefono, $fecha_nacimiento);

            if ($stmt->execute()) {
                $exito = "Usuario registrado correctamente, ya puedes iniciar sesión";
            } else {
                $error = "Error al registrar el usuario";
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
    <title>Registro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="card">
    <h2>Crear cuenta</h2>
    <p class="subtitle">Bienvenido de nuevo</p>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($exito): ?>
        <p style="color:green;"><?= $exito ?></p>
    <?php endif; ?>
                                                                                                  
    <form method="POST" action="">
        <div class="form-group">
        <label>Cédula</label>
        <input type=    "text" name="cedula" required>
        </div>
        <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" required>
        </div>
        <div class="form-group">
        <label>Correo</label>
        <input type="email" name="correo" required>
        </div>  
        <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" required>
        </div>
        <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono">
        </div>
        <div class="form-group">
        <label>Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento">
        </div>

        <button type="submit">Registrarse</button>

    </form>
    <div class="links">
        <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
    </div>

</body>
</html>