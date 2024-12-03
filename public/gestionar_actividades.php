<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Manejar la adición de actividades
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $fecha = $conn->real_escape_string($_POST['fecha']);

    $sql = "INSERT INTO actividades_recreativas (nombre, descripcion, fecha) VALUES ('$nombre', '$descripcion', '$fecha')";
    $conn->query($sql);
}

// Manejar la eliminación de actividades
if (isset($_GET['eliminar'])) {
    $id = $conn->real_escape_string($_GET['eliminar']);
    $sql = "DELETE FROM actividades_recreativas WHERE id = '$id'";
    $conn->query($sql);
}

// Manejar la edición de actividades
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $fecha = $conn->real_escape_string($_POST['fecha']);

    $sql = "UPDATE actividades_recreativas SET nombre = '$nombre', descripcion = '$descripcion', fecha = '$fecha' WHERE id = '$id'";
    $conn->query($sql);
}

// Obtener lista de actividades
$sql = "SELECT * FROM actividades_recreativas ORDER BY fecha";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Actividades Recreativas</title>
    <link rel="stylesheet" href="css/gestionar_actividades.css">
    <link rel="icon" type="image/x-icon" href="./img/icono.jpg">
</head>

<body>
    <!-- Video de fondo -->
    <video autoplay muted loop id="background-video">
        <source src="./video/actividades.mp4" type="video/mp4">
        Tu navegador no soporta video.
    </video>

    <div class="overlay">
        <h2>Gestionar Actividades Recreativas</h2>

        <h3>Agregar Nueva Actividad</h3>
        <form method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br>

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required><br>

            <button type="submit" name="agregar">Agregar Actividad</button>
        </form>

        <div class="center-container">
            <h3>Lista de Actividades</h3>
            <table border="1">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                        <td>
                            <a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar esta actividad?');">Eliminar</a>
                            <a href="#" onclick="mostrarFormularioEdicion(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
            <p><a href="admin_panel.php" class="back-link">Volver al Panel de Administrador</a></p>
        </div>

        <div id="formularioEdicion" style="display:none;">
            <h3>Editar Actividad</h3>
            <form method="post">
                <input type="hidden" id="edit_id" name="id">
                <label for="edit_nombre">Nombre:</label>
                <input type="text" id="edit_nombre" name="nombre" required><br>
                <label for="edit_descripcion">Descripción:</label>
                <textarea id="edit_descripcion" name="descripcion" required></textarea><br>
                <label for="edit_fecha">Fecha:</label>
                <input type="date" id="edit_fecha" name="fecha" required><br>
                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        </div>

        <script>
            function mostrarFormularioEdicion(actividad) {
                document.getElementById('edit_id').value = actividad.id;
                document.getElementById('edit_nombre').value = actividad.nombre;
                document.getElementById('edit_descripcion').value = actividad.descripcion;
                document.getElementById('edit_fecha').value = actividad.fecha;
                document.getElementById('formularioEdicion').style.display = 'block';
            }
        </script>
    </div>
</body>

</html>
