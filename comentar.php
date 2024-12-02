<?php
require_once 'config.php';
verificar_sesion();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: foro.php");
    exit();
}

$publicacion_id = $conn->real_escape_string($_GET['id']);

// Obtener detalles de la publicación
$sql_publicacion = "SELECT p.*, u.nombre as autor FROM publicaciones_foro p 
                    JOIN usuarios u ON p.usuario_id = u.id 
                    WHERE p.id = '$publicacion_id'";
$result_publicacion = $conn->query($sql_publicacion);

if ($result_publicacion->num_rows == 0) {
    header("Location: foro.php");
    exit();
}

$publicacion = $result_publicacion->fetch_assoc();

// Procesar el nuevo comentario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentario'])) {
    $comentario = $conn->real_escape_string($_POST['comentario']);
    $usuario_id = $_SESSION['usuario_id'];

    $sql_insertar_comentario = "INSERT INTO comentarios_foro (publicacion_id, usuario_id, contenido) 
                                VALUES ('$publicacion_id', '$usuario_id', '$comentario')";
    
    if ($conn->query($sql_insertar_comentario) === TRUE) {
        $mensaje = "Comentario agregado exitosamente.";
    } else {
        $error = "Error al agregar el comentario: " . $conn->error;
    }
}

// Obtener comentarios existentes
$sql_comentarios = "SELECT c.*, u.nombre as autor FROM comentarios_foro c 
                    JOIN usuarios u ON c.usuario_id = u.id 
                    WHERE c.publicacion_id = '$publicacion_id' 
                    ORDER BY c.fecha_comentario DESC";
$result_comentarios = $conn->query($sql_comentarios);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios - <?php echo htmlspecialchars($publicacion['titulo']); ?></title>
    <link rel="stylesheet" href="./css/comentar.css">
    <link rel="icon" type="image/x-icon" href="./img/icono.jpg">
</head>
<body>
    <!-- Video de fondo -->
    <video autoplay muted loop id="background-video">
        <source src="../video/salon1.mp4" type="video/mp4">
        Tu navegador no soporta video en HTML5.
    </video>
    <div class="content">
    <h2>Comentarios para: <?php echo htmlspecialchars($publicacion['titulo']); ?></h2>
    
    <div class="publicacion">
        <h3><?php echo htmlspecialchars($publicacion['titulo']); ?></h3>
        <p><?php echo htmlspecialchars($publicacion['contenido']); ?></p>
        <small>Publicado por <?php echo htmlspecialchars($publicacion['autor']); ?> el <?php echo $publicacion['fecha_publicacion']; ?></small>
    </div>

    <h3>Comentarios</h3>
    <?php if ($result_comentarios->num_rows > 0): ?>
        <?php while ($comentario = $result_comentarios->fetch_assoc()): ?>
            <div class="comentario">
                <p><?php echo htmlspecialchars($comentario['contenido']); ?></p>
                <small>Comentado por <?php echo htmlspecialchars($comentario['autor']); ?> el <?php echo $comentario['fecha_comentario']; ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No hay comentarios aún.</p>
    <?php endif; ?>

    <h3>Agregar un comentario</h3>
    <?php if (isset($mensaje)): ?>
        <p class="success"><?php echo $mensaje; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <textarea name="comentario" rows="4" cols="50" required></textarea><br>
        <button type="submit">Enviar comentario</button>
    </form>

    <p><a href="foro.php">Volver al Foro</a></p>
    </div>
</body>
</html>