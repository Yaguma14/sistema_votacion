<?php
require_once 'config.php';
verificar_sesion();

$usuario_id = $_SESSION['usuario_id'];

// Obtener notificaciones
$notificaciones = obtener_notificaciones($usuario_id);

// Manejar la búsqueda
$termino_busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Definir la consulta SQL base
$sql = "SELECT p.*, u.nombre as autor FROM publicaciones_foro p 
        JOIN usuarios u ON p.usuario_id = u.id";

if ($termino_busqueda) {
    $termino = $conn->real_escape_string($termino_busqueda);
    $sql .= " WHERE p.titulo LIKE '%$termino%' OR p.contenido LIKE '%$termino%'";
}

$sql .= " ORDER BY p.fecha_publicacion DESC";

// Verificar si las votaciones están abiertas
$sql_votaciones = "SELECT votaciones_abiertas FROM configuracion_sistema LIMIT 1";
$result_votaciones = $conn->query($sql_votaciones);
$votaciones_abiertas = $result_votaciones->fetch_assoc()['votaciones_abiertas'];

// Verificar si el estudiante ya ha votado
$sql_check_voto = "SELECT id FROM votos_candidatos WHERE usuario_id = $usuario_id LIMIT 1";
$result_check_voto = $conn->query($sql_check_voto);
$ya_voto = $result_check_voto->num_rows > 0;

// Paginación
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$publicaciones_por_pagina = 10;

// Obtener el total de publicaciones
$result_count = $conn->query($sql);
$total_publicaciones = $result_count->num_rows;
$total_paginas = ceil($total_publicaciones / $publicaciones_por_pagina);
$inicio = ($pagina_actual - 1) * $publicaciones_por_pagina;
$sql .= " LIMIT $inicio, $publicaciones_por_pagina";

// Ejecutar la consulta final
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro Estudiantil</title>
    <link rel="stylesheet" href="css/foro.css">
</head>
<body>
    <!-- Video de fondo -->
    <video autoplay muted loop id="background-video">
        <source src="video/salon3.mp4" type="video/mp4">
        Tu navegador no soporta videos.
    </video>

    <!-- Contenido principal -->
    <div class="content">
        <h2>Foro Estudiantil</h2>

        <!-- Menú de usuario -->
        <div class="user-menu">
            <a href="perfil.php">Mi Perfil</a>
            <a href="#" id="notificaciones-toggle">Notificaciones (<?php echo $notificaciones->num_rows; ?>)</a>
            <a href="logout.php">Cerrar Sesión</a>
        </div>

        <!-- Panel de notificaciones -->
        <div id="notificaciones-panel" style="display: none;">
            <?php while ($notificacion = $notificaciones->fetch_assoc()): ?>
                <div class="notificacion <?php echo $notificacion['leida'] ? 'leida' : ''; ?>">
                    <?php echo htmlspecialchars($notificacion['mensaje']); ?>
                </div>
            <?php endwhile; ?>
        </div>
<!-- Estado de votaciones -->
<?php if ($votaciones_abiertas): ?>
            <?php if ($ya_voto): ?>
                <p class="mensaje-votacion">Ya has emitido tu voto. ¡Gracias por participar!</p>
            <?php else: ?>
                <p><a href="votaciones.php" class="boton-votar">¡Las votaciones están abiertas! Haz clic aquí para votar.</a></p>
            <?php endif; ?>
        <?php else: ?>
            <p>Las votaciones se abrirán pronto.</p>
        <?php endif; ?>

        <!-- Barra de búsqueda -->
        <form method="get" action="foro.php" class="buscador">
            <input type="text" name="buscar" placeholder="Buscar en el foro..." value="<?php echo htmlspecialchars($termino_busqueda); ?>" class="barra-buscar">
            <button type="submit" class="boton-buscar">Buscar</button>
        </form>

        <!-- Novedades y publicaciones -->
        <h3>Novedades y Publicaciones</h3>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="publicacion">
                <h4><?php echo htmlspecialchars($row['titulo']); ?></h4>
                <p><?php echo htmlspecialchars($row['contenido']); ?></p>
                <small>Publicado por <?php echo htmlspecialchars($row['autor']); ?> el <?php echo $row['fecha_publicacion']; ?></small>
                <!-- Campo de etiquetas -->
                <div class="etiquetas">
                    <?php
                    // Ejemplo de etiquetas ficticias
                    $etiquetas = ["Educación", "Foro"];
                    foreach ($etiquetas as $etiqueta) {
                        echo "<span class='etiqueta'>" . htmlspecialchars($etiqueta) . "</span>";
                    }
                    ?>
                </div>
                <a href="comentar.php?id=<?php echo $row['id']; ?>" class="enlace-comentar">Comentar</a>
            </div>
        <?php endwhile; ?>

        <!-- Paginación -->
        <div class="paginacion">
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?php echo $i; ?><?php echo $termino_busqueda ? '&buscar=' . urlencode($termino_busqueda) : ''; ?>" <?php echo ($i == $pagina_actual) ? 'class="activa"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Script para manejar las notificaciones -->
    <script>
    document.getElementById('notificaciones-toggle').addEventListener('click', function(e) {
        e.preventDefault();
        var panel = document.getElementById('notificaciones-panel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    });
    </script>
</body>
</html>