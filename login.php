<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT id, nombre, contrasena, rol FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            if ($usuario['rol'] == 'administrador') {
                header("Location: admin_panel.php");
            } else {
                header("Location: foro.php");
            }
            exit();
        }
    }

    $error = "Correo o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style-login.css">
</head>

<body>
    <!-- Video de fondo -->
    <video autoplay muted loop id="background-video">
        <source src="video/video 1.mp4" type="video/mp4">
        Tu navegador no soporta el formato de video.
    </video>

    <div class="container">
        <div class="content">
            <h1 class="title">Iniciar Sesión</h1>
            <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
            <form method="post">
                <div class="form-group">
                    <label for="correo" class="label">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" class="input-field" required>
                </div>
                <div class="form-group">
                    <label for="contrasena" class="label">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" class="input-field" required>
                </div>
                <button type="submit" class="button">Iniciar Sesión</button>
            </form>
            <p><a href="registro.php" class="link">Registrarse</a></p>
        </div>
    </div>
</body>

</html>
