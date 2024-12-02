<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Manejar la eliminación de usuarios
if (isset($_GET['eliminar'])) {
    $id = $conn->real_escape_string($_GET['eliminar']);
    $sql = "DELETE FROM usuarios WHERE id = '$id' AND rol != 'administrador'";
    $conn->query($sql);
}

// Manejar la edición de usuarios
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $rol = $conn->real_escape_string($_POST['rol']);

    $sql = "UPDATE usuarios SET nombre = '$nombre', correo = '$correo', rol = '$rol' WHERE id = '$id'";
    $conn->query($sql);
}

// Obtener lista de usuarios
$sql = "SELECT * FROM usuarios ORDER BY nombre";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link rel="stylesheet" href="css/gestionar_usuarios.css">
</head>

<body>
    <!-- Contenedor de video de fondo -->
    <div class="video-container">
        <video autoplay muted loop class="background-video">
            <source src="./video/usuarios.mp4" type="video/mp4">
            Tu navegador no soporta la etiqueta de video.
        </video>
    </div>
    <div class="video-container">
        <h2>Gestionar Usuarios</h2>
        <table border="1">
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['correo']); ?></td>
                    <td><?php echo htmlspecialchars($row['rol']); ?></td>
                    <td>
                        <a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">Eliminar</a>
                        <a href="#" onclick="mostrarFormularioEdicion(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div id="formularioEdicion" style="display:none;">
            <h3>Editar Usuario</h3>
            <form method="post">
                <input type="hidden" id="edit_id" name="id">
                <label for="edit_nombre">Nombre:</label>
                <input type="text" id="edit_nombre" name="nombre" required><br>
                <label for="edit_correo">Correo:</label>
                <input type="email" id="edit_correo" name="correo" required><br>
                <label for="edit_rol">Rol:</label>
                <select id="edit_rol" name="rol">
                    <option value="estudiante">Estudiante</option>
                    <option value="administrador">Administrador</option>
                </select><br>
                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        </div>

        <script>
            function mostrarFormularioEdicion(usuario) {
                document.getElementById('edit_id').value = usuario.id;
                document.getElementById('edit_nombre').value = usuario.nombre;
                document.getElementById('edit_correo').value = usuario.correo;
                document.getElementById('edit_rol').value = usuario.rol;
                document.getElementById('formularioEdicion').style.display = 'block';
            }
        </script>

        <center><p><a href="admin_panel.php">Volver al Panel de Administrador</a></p></center>

    </div>
</body>

</html>