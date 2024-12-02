<?php
require_once 'config.php';

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función para redirigir con un mensaje
function redirigir($url, $mensaje, $tipo = 'error') {
    $_SESSION['mensaje'] = $mensaje;
    $_SESSION['tipo_mensaje'] = $tipo;
    header("Location: $url");
    exit();
}

// Verificar si el usuario es superadministrador o está autenticado
if (!es_superadministrador() && !isset($_SESSION['admin_auth'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_password'])) {
        if ($_POST['admin_password'] === ADMIN_REGISTER_PASSWORD) {
            $_SESSION['admin_auth'] = true;
        } else {
            redirigir($_SERVER['PHP_SELF'], "Contraseña incorrecta");
        }
    } else {
        // Mostrar formulario de autenticación
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Autenticación para Registro de Administrador</title>
            <link rel="stylesheet" href="./css/style-registrar-admin.css">
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
                <?php
                if (isset($_SESSION['mensaje'])) {
                    echo "<p class='{$_SESSION['tipo_mensaje']}'>{$_SESSION['mensaje']}</p>";
                    unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
                }
                ?>
                <form method="post">
                    <div class="form-group">
                        <label for="admin_password" class="label">Contraseña de Acceso:</label>
                        <input type="password" id="admin_password" name="admin_password" class="input-field" required>
                    </div>
                    <button type="submit" class="button">Acceder</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Procesar el registro del administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = 'administrador';

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $correo, $contrasena, $rol);

    if ($stmt->execute()) {
        redirigir($_SERVER['PHP_SELF'], "Administrador registrado con éxito", 'success');
    } else {
        redirigir($_SERVER['PHP_SELF'], "Error al registrar: " . $stmt->error);
    }
    $stmt->close();
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
        if (isset($_SESSION['mensaje'])) {
            echo "<p class='{$_SESSION['tipo_mensaje']}'>{$_SESSION['mensaje']}</p>";
            unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
        }
        ?>
        <form method="post">
            <div class="form-group">
                <label for="nombre" class="label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="input-field" required>
            </div>
            <div class="form-group">
                <label for="correo" class="label">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" class="input-field" required>
            </div>
            <div class="form-group">
                <label for="contrasena" class="label">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" class="input-field" required>
            </div>
            <button type="submit" name="registrar" class="button">Registrar Administrador</button>
        </form>
        <p class="center"><a href="admin_panel.php" class="link">Volver al Panel de Administrador</a></p>
    </div>
</body>
</html>