<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Manejar la adición de candidatos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $cargo = $conn->real_escape_string($_POST['cargo']);
    $propuestas = $conn->real_escape_string($_POST['propuestas']);

    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = 'up';
        $foto_nombre = basename($_FILES['foto']['name']);
        $foto_destino = $upload_dir . $foto_nombre;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_destino)) {
            $foto = $foto_destino;
        }
    }

    $sql = "INSERT INTO candidatos (nombre, cargo, propuestas, foto) VALUES ('$nombre', '$cargo', '$propuestas', '$foto')";
    
    if ($conn->query($sql) === TRUE) {
        $mensaje = "Candidato agregado con éxito.";
    } else {
        $error = "Error al agregar el candidato: " . $conn->error;
    }
}

// Manejar la edición de candidatos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $cargo = $conn->real_escape_string($_POST['cargo']);
    $propuestas = $conn->real_escape_string($_POST['propuestas']);

    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = 'uploads/';
        $foto_nombre = basename($_FILES['foto']['name']);
        $foto_destino = $upload_dir . $foto_nombre;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_destino)) {
            $foto = $foto_destino;
        }
    }

    $sql = "UPDATE candidatos SET nombre = '$nombre', cargo = '$cargo', propuestas = '$propuestas'";
    if ($foto) {
        $sql .= ", foto = '$foto'";
    }
    $sql .= " WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Candidato actualizado con éxito.";
    } else {
        $error = "Error al actualizar el candidato: " . $conn->error;
    }
}

// Manejar la eliminación de candidatos
if (isset($_GET['eliminar'])) {
    $id = $conn->real_escape_string($_GET['eliminar']);
    $sql = "DELETE FROM candidatos WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        $mensaje = "Candidato eliminado con éxito.";
    } else {
        $error = "Error al eliminar el candidato: " . $conn->error;
    }
}

// Obtener lista de candidatos
$sql = "SELECT * FROM candidatos ORDER BY nombre";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Candidatos</title>
    <link rel="stylesheet" href="css/gestionar_candidatos.css">
</head>
<body>

    <!-- Contenedor para el video de fondo -->
    <div class="video-container">
        <video class="background-video" autoplay muted loop>
            <source src="video/salon4.mp4" type="video/mp4">
            Tu navegador no soporta el video.
        </video>
    </div>

    <div class="content">
        <h2>Gestionar Candidatos</h2>

        <?php if (isset($mensaje)): ?>
            <p class="success"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <h3>Agregar Nuevo Candidato</h3>
        <center>
        <form method="post" enctype="multipart/form-data">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br>

            <label for="cargo">Cargo:</label>
            <select id="cargo" name="cargo" required>
                <option value="personero">Personero</option>
                <option value="cavildante">Cavildante</option>
                <option value="contralor">Contralor</option>
            </select><br>

            <label for="foto">Foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*"><br>

            <label for="propuestas">Propuestas:</label>
            <textarea id="propuestas" name="propuestas" required></textarea><br>

            <button type="submit" name="agregar">Agregar Candidato</button>
        </form></center>

        <h3>Lista de Candidatos</h3>
        <table border="1">
            <tr>
                <th>Nombre</th>
                <th>Cargo</th>
                <th>Foto</th>
                <th>Propuestas</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['cargo']); ?></td>
                    <td>
                        <?php if ($row['foto']): ?>
                            <img src="<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>" width="100">
                        <?php else: ?>
                            Sin foto
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['propuestas']); ?></td>
                    <td>
                        <a href="#" onclick="mostrarFormularioEdicion(<?php echo htmlspecialchars(json_encode($row)); ?>)">Editar</a>
                        <a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este candidato?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div id="formularioEdicion" style="display:none;">
            <h3>Editar Candidato</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <label for="edit_nombre">Nombre:</label>
                <input type="text" id="edit_nombre" name="nombre" required><br>
                <label for="edit_cargo">Cargo:</label>
                <select id="edit_cargo" name="cargo" required>
                    <option value="personero">Personero</option>
                    <option value="cavildante">Cavildante</option>
                    <option value="contralor">Contralor</option>
                </select><br>
                <label for="edit_foto">Foto:</label>
                <input type="file" id="edit_foto" name="foto" accept="image/*"><br>
                <label for="edit_propuestas">Propuestas:</label>
                <textarea id="edit_propuestas" name="propuestas" required></textarea><br>
                <button type="submit" name="editar">Guardar Cambios</button>
            </form>
        </div>

        <script>
        function mostrarFormularioEdicion(candidato) {
            document.getElementById('edit_id').value = candidato.id;
            document.getElementById('edit_nombre').value = candidato.nombre;
            document.getElementById('edit_cargo').value = candidato.cargo;
            document.getElementById('edit_propuestas').value = candidato.propuestas;
            document.getElementById('formularioEdicion').style.display = 'block';
        }
        </script>

        <center><p><a href="admin_panel.php">Volver al Panel de Administrador</a></p></center>
    </div>
</body>
</html>
