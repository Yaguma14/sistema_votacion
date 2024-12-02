<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Obtener el estado actual de las votaciones
$sql = "SELECT votaciones_abiertas FROM configuracion_sistema LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $votaciones_abiertas = $row['votaciones_abiertas'];
} else {
    $sql = "INSERT INTO configuracion_sistema (votaciones_abiertas) VALUES (0)";
    $conn->query($sql);
    $votaciones_abiertas = 0;
}

// Manejar la actualización del estado de las votaciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_estado'])) {
    $nuevo_estado = $_POST['estado'] == '1' ? 1 : 0;
    $sql = "UPDATE configuracion_sistema SET votaciones_abiertas = $nuevo_estado";
    
    if ($conn->query($sql) === TRUE) {
        $votaciones_abiertas = $nuevo_estado;
        $mensaje = "El estado de las votaciones ha sido actualizado.";
        if ($nuevo_estado == 1) {
            crear_notificacion_global("Las votaciones están ahora abiertas. ¡No olvides participar!", "votaciones");
        } else {
            crear_notificacion_global("Las votaciones han sido cerradas. Gracias por tu participación.", "votaciones");
        }
        registrar_log($_SESSION['usuario_id'], 'Actualización estado votaciones', "Nuevo estado: " . ($nuevo_estado ? 'Abiertas' : 'Cerradas'));
    } else {
        $error = "Error al actualizar el estado de las votaciones: " . $conn->error;
    }
}

// Obtener estadísticas de votación
$sql_estadisticas = "
    SELECT 
        (SELECT COUNT(*) FROM votos_candidatos) as total_votos_candidatos,
        (SELECT COUNT(*) FROM votos_actividades) as total_votos_actividades,
        (SELECT COUNT(*) FROM usuarios WHERE rol = 'estudiante') as total_estudiantes,
        (SELECT COUNT(*) FROM candidatos WHERE cargo = 'personero') as total_personeros,
        (SELECT COUNT(*) FROM candidatos WHERE cargo = 'cavildante') as total_cavildantes,
        (SELECT COUNT(*) FROM candidatos WHERE cargo = 'contralor') as total_contralores,
        (SELECT COUNT(*) FROM actividades_recreativas) as total_actividades
";
$result_estadisticas = $conn->query($sql_estadisticas);
$estadisticas = $result_estadisticas->fetch_assoc();

// Calcular porcentajes
$porcentaje_participacion_candidatos = $estadisticas['total_estudiantes'] > 0 ? 
    round(($estadisticas['total_votos_candidatos'] / $estadisticas['total_estudiantes']) * 100, 2) : 0;
$porcentaje_participacion_actividades = $estadisticas['total_estudiantes'] > 0 ? 
    round(($estadisticas['total_votos_actividades'] / $estadisticas['total_estudiantes']) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Votaciones</title>
    <link rel="stylesheet" href="css/gestionar_votaciones.css">
</head>
<body>
    
    <!-- Video de fondo -->
    <div class="video-background">
        <video autoplay muted loop id="video-background">
            <source src="video/votaciones.mp4" type="video/mp4">
            Tu navegador no soporta el formato de video.
        </video>
    </div>

    <div class="content">
        <h2>Gestionar Votaciones</h2>

        <?php if (isset($mensaje)): ?>
            <p class="success"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <div class="estado-votaciones">
            <h3>Estado actual de las votaciones</h3>
            <p>Las votaciones están actualmente: <strong><?php echo $votaciones_abiertas ? 'Abiertas' : 'Cerradas'; ?></strong></p>

            <form method="post">
                <label for="estado">Cambiar estado de las votaciones:</label>
                <select name="estado" id="estado">
                    <option value="1" <?php echo $votaciones_abiertas ? 'selected' : ''; ?>>Abiertas</option>
                    <option value="0" <?php echo !$votaciones_abiertas ? 'selected' : ''; ?>>Cerradas</option>
                </select>
                <button type="submit" name="actualizar_estado">Actualizar Estado</button>
            </form>
        </div>

        <h3>Estadísticas de Votación</h3>
        <div class="estadisticas">
            <div class="metrica">
                <h4>Participación General</h4>
                <p>Total de estudiantes: <?php echo $estadisticas['total_estudiantes']; ?></p>
                <p>Votos para candidatos: <?php echo $estadisticas['total_votos_candidatos']; ?> (<?php echo $porcentaje_participacion_candidatos; ?>%)</p>
                <p>Votos para actividades: <?php echo $estadisticas['total_votos_actividades']; ?> (<?php echo $porcentaje_participacion_actividades; ?>%)</p>
            </div>
            <div class="metrica">
                <h4>Candidatos</h4>
                <p>Total de personeros: <?php echo $estadisticas['total_personeros']; ?></p>
                <p>Total de cabildantes: <?php echo $estadisticas['total_cavildantes']; ?></p>
                <p>Total de contralores: <?php echo $estadisticas['total_contralores']; ?></p>
            </div>
            <div class="metrica">
                <h4>Actividades Recreativas</h4>
                <p>Total de actividades: <?php echo $estadisticas['total_actividades']; ?></p>
            </div>
        </div>

        <center><p><a href="admin_panel.php">Volver al Panel de Administrador</a></p></center>
    </div>
</body>
</html>
