<?php
require_once 'config.php';

// Verificar si se ha enviado la contraseña correcta
if (!isset($_SESSION['admin_auth']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_password'])) {
    if ($_POST['admin_password'] === ADMIN_REGISTER_PASSWORD) {
        $_SESSION['admin_auth'] = true;
    } else {
        $error = "Contraseña incorrecta";
    }
}

// Verificar si el usuario está autenticado como superadministrador o ha proporcionado la contraseña correcta
if (!es_superadministrador() && !isset($_SESSION['admin_auth'])) {
    if (!isset($error)) {
        $error = "Por favor, ingrese la contraseña para acceder a esta página.";
    }
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Autenticación para Registro de Administrador</title>
        <link rel="stylesheet" href="css/style-registrar-admin.css">
    </head>

    <body>
        <div class="video-background">
            <video autoplay muted loop>
                <source src="video/video 3.mp4" type="video/mp4">
                Tu navegador no soporta el video.
            </video>
        </div>
        <div class="container">
            <h2 class="title">Autenticación Requerida</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="post">
                <label for="admin_password" class="label">Contraseña de Acceso:</label>
                <input type="password" id="admin_password" name="admin_password" class="input-field" required>
                <button type="submit" class="button">Acceder</button>
            </form>
        </div>
    </body>

    </html>
<?php
    exit();
}

// Procesar el registro del administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = 'administrador';

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Administrador registrado con éxito";
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
    <title>Registrar Administrador</title>
    <link rel="stylesheet" href="css/style-registrar-admin.css">
</head>

<body>
    <div class="video-background">
        <video autoplay muted loop>
            <source src="video/video 3.mp4" type="video/mp4">
            Tu navegador no soporta el video.
        </video>
    </div>
    <div class="container">
        <h2 class="title">Registrar Nuevo Administrador</h2>
        <?php
        if (isset($mensaje)) echo "<p class='success'>$mensaje</p>";
        if (isset($error)) echo "<p class='error'>$error</p>";
        ?>
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

            <button type="submit" name="registrar" class="button">Registrar Administrador</button>
        </form>
        <center><p><a href="admin_panel.php" class="link">Volver al Panel de Administrador</a></p></center>
    </div>
</body>

</html>
