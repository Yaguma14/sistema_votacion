<?php
require_once 'config.php';
verificar_sesion();

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = $_POST['contrasena'] ? password_hash($_POST['contrasena'], PASSWORD_DEFAULT) : '';

    if (validar_nombre($nombre) && validar_email($correo)) {
        $sql = "UPDATE usuarios SET nombre = '$nombre', correo = '$correo'";
        if ($contrasena) {
            $sql .= ", contrasena = '$contrasena'";
        }
        $sql .= " WHERE id = $usuario_id";

        if ($conn->query($sql) === TRUE) {
            $mensaje = "Perfil actualizado con éxito.";
            registrar_log($usuario_id, 'Actualización de perfil', "Usuario $usuario_id actualizó su perfil");
        } else {
            $error = "Error al actualizar el perfil: " . $conn->error;
        }
    } else {
        $error = "Datos inválidos. Por favor, verifica la información ingresada.";
    }
}

$sql = "SELECT nombre, correo FROM usuarios WHERE id = $usuario_id";
$result = $conn->query($sql);
$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Mi Perfil</h2>
    
    <?php if (isset($mensaje)): ?>
        <p class="success"><?php echo $mensaje; ?></p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

        <label for="correo">Email:</label>
        <input type="correo" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>

        <label for="contrasena">Nueva Contraseña (dejar en blanco para no cambiar):</label>
        <input type="password" id="contrasena" name="contrasena">

        <button type="submit">Actualizar Perfil</button>
    </form>

    <p><a href="foro.php">Volver al Foro</a></p>
</body>
</html>