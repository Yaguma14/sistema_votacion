<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = 'estudiante'; // Por defecto, todos los nuevos registros son estudiantes

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Error al registrar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles/style-registro.css">
</head>
<body>
<div class="container">
        <h2 class="title">Registro de Usuario</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <div class="form-group">
                <label for="nombre" class="label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="input-field" required><br>
            </div>

            <div class="form-group">
                <label for="correo" class="label">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" class="input-field" required><br>
            </div>

            <div class="form-group">
                <label for="contrasena" class="label">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" class="input-field" required><br>
            </div>

            <button type="submit" class="button">Registrarse</button>
        </form>
        <p><a href="login.php" class="link">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>