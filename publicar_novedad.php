<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['publicar'])) {
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $contenido = $conn->real_escape_string($_POST['contenido']);
        $usuario_id = $_SESSION['usuario_id'];

        $sql = "INSERT INTO publicaciones_foro (usuario_id, titulo, contenido) VALUES ('$usuario_id', '$titulo', '$contenido')";
        
        if ($conn->query($sql) === TRUE) {
            $mensaje = "La novedad ha sido publicada exitosamente.";
        } else {
            $error = "Error al publicar la novedad: " . $conn->error;
        }
    } elseif (isset($_POST['editar'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $contenido = $conn->real_escape_string($_POST['contenido']);

        $sql = "UPDATE publicaciones_foro SET titulo = '$titulo', contenido = '$contenido' WHERE id = '$id'";
        
        if ($conn->query($sql) === TRUE) {
            $mensaje = "La novedad ha sido actualizada exitosamente.";
        } else {
            $error = "Error al actualizar la novedad: " . $conn->error;
        }
    }
}

if (isset($_GET['eliminar'])) {
    $id = $conn->real_escape_string($_GET['eliminar']);
    
    // Primero, eliminar los comentarios asociados
    $sql = "DELETE FROM comentarios_foro WHERE publicacion_id = '$id'";
    $conn->query($sql);

    // Luego, eliminar la publicación
    $sql = "DELETE FROM publicaciones_foro WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        $mensaje = "La novedad y sus comentarios han sido eliminados exitosamente.";
    } else {
        $error = "Error al eliminar la novedad: " . $conn->error;
    }
}

// Obtener las últimas novedades publicadas
$sql_ultimas_novedades = "SELECT pf.*, u.nombre as autor 
                          FROM publicaciones_foro pf 
                          JOIN usuarios u ON pf.usuario_id = u.id 
                          WHERE u.rol = 'administrador' 
                          ORDER BY pf.fecha_publicacion DESC";
$result_ultimas_novedades = $conn->query($sql_ultimas_novedades);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Novedad</title>
    <link rel="stylesheet" href="css/style-publicar-novedad.css">
</head>
<body>
    <div class="container">
        <!-- Video de fondo -->
        <video id="background-video" autoplay muted loop>
            <source src="video/video 2.mp4" type="video/mp4">
            Tu navegador no soporta videos.
        </video>

        <div class="content">
            <h2>Publicar Novedad</h2>

            <!-- Mensajes -->
            <?php if ($mensaje): ?>
                <div class="success" id="mensaje-exito">
                    <?php echo $mensaje; ?>
                </div>
                <script>
                    setTimeout(function() {
                        document.getElementById('mensaje-exito').style.opacity = '0';
                        setTimeout(function() {
                            document.getElementById('mensaje-exito').style.display = 'none';
                        }, 300);
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error" id="mensaje-error">
                    <?php echo $error; ?>
                </div>
                <script>
                    setTimeout(function() {
                        document.getElementById('mensaje-error').style.opacity = '0';
                        setTimeout(function() {
                            document.getElementById('mensaje-error').style.display = 'none';
                        }, 300);
                    }, 3000);
                </script>
            <?php endif; ?>

            <!-- Formulario para publicar/editar novedad -->
            <form method="post" id="formNovedad">
                <input type="hidden" id="id" name="id">
                <div class="form-group">
                    <label for="titulo">Título de la novedad:</label>
                    <input type="text" id="titulo" name="titulo" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="contenido">Contenido de la novedad:</label>
                    <textarea id="contenido" name="contenido" required rows="6"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="publicar" id="btnPublicar">Publicar Novedad</button>
                    <button type="submit" name="editar" id="btnEditar" style="display:none;">Actualizar Novedad</button>
                </div>
            </form>

            <h3>Últimas Novedades Publicadas</h3>
            <!-- Tabla de novedades -->
            <div class="table-container">
                <?php if ($result_ultimas_novedades->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Fecha de Publicación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $novedades_mostradas = array(); // Array para controlar duplicados
                            while ($row = $result_ultimas_novedades->fetch_assoc()): 
                                // Verificar si ya se mostró esta novedad
                                $novedad_key = $row['titulo'] . $row['fecha_publicacion'];
                                if (!in_array($novedad_key, $novedades_mostradas)):
                                    $novedades_mostradas[] = $novedad_key;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($row['autor']); ?></td>
                                    <td><?php echo $row['fecha_publicacion']; ?></td>
                                    <td class="action-buttons">
                                        <button class="btn-editar" onclick="editarNovedad(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                            Editar
                                        </button>
                                        <a href="?eliminar=<?php echo $row['id']; ?>" 
                                           class="btn-eliminar" 
                                           onclick="return confirm('¿Estás seguro de que quieres eliminar esta novedad y todos sus comentarios?');">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endif;
                            endwhile; 
                            ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay novedades publicadas recientemente.</p>
                <?php endif; ?>
            </div>

            <p><a href="admin_panel.php" class="back-link">Volver al Panel de Administrador</a></p>
        </div>
    </div>

    <script>
    function editarNovedad(novedad) {
        document.getElementById('id').value = novedad.id;
        document.getElementById('titulo').value = novedad.titulo;
        document.getElementById('contenido').value = novedad.contenido;
        document.getElementById('btnPublicar').style.display = 'none';
        document.getElementById('btnEditar').style.display = 'inline-block';
    }
    </script>
</body>
</html>