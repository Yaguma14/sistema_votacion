<?php
require_once 'config.php';
verificar_sesion();

// Verificar si las votaciones están abiertas
$sql_votaciones = "SELECT votaciones_abiertas FROM configuracion_sistema LIMIT 1";
$result_votaciones = $conn->query($sql_votaciones);
$votaciones_abiertas = $result_votaciones->fetch_assoc()['votaciones_abiertas'];

if (!$votaciones_abiertas) {
    header("Location: foro.php");
    exit();
}

// Verificar si el estudiante ya ha votado
$usuario_id = $_SESSION['usuario_id'];
$sql_check_voto = "SELECT id FROM votos_candidatos WHERE usuario_id = $usuario_id LIMIT 1";
$result_check_voto = $conn->query($sql_check_voto);

if ($result_check_voto->num_rows > 0) {
    // El estudiante ya ha votado, redirigir al foro
    header("Location: foro.php?mensaje=ya_votaste");
    exit();
}

// Obtener candidatos
$sql_candidatos = "SELECT * FROM candidatos";
$result_candidatos = $conn->query($sql_candidatos);

// Obtener actividades recreativas
$sql_actividades = "SELECT * FROM actividades_recreativas";
$result_actividades = $conn->query($sql_actividades);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidato_id = $conn->real_escape_string($_POST['candidato']);
    $actividad_id = $conn->real_escape_string($_POST['actividad']);
    $usuario_id = $_SESSION['usuario_id'];

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Registrar voto para candidato
        $sql_voto_candidato = "INSERT INTO votos_candidatos (usuario_id, candidato_id) VALUES ('$usuario_id', '$candidato_id')";
        $conn->query($sql_voto_candidato);

        // Registrar voto para actividad recreativa
        $sql_voto_actividad = "INSERT INTO votos_actividades (usuario_id, actividad_id) VALUES ('$usuario_id', '$actividad_id')";
        $conn->query($sql_voto_actividad);

        // Confirmar transacción
        $conn->commit();

        // Redirigir al foro con mensaje de éxito
        header("Location: foro.php?mensaje=voto_exitoso");
        exit();
    } catch (Exception $e) {
        // Si algo sale mal, deshacer los cambios
        $conn->rollback();
        $error = "Error al registrar el voto: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votaciones</title>
    <link rel="stylesheet" href="css/style-votaciones.css">
</head>
<body>
    <!-- Video de fondo -->
    <div class="background-video">
        <video autoplay loop muted>
            <source src="video/votaciones.mp4" type="video/mp4">
            Tu navegador no soporta el elemento de video.
        </video>
    </div>

    <div class="container">
        <h2>Votaciones</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <h3>Candidatos</h3>
            <?php while ($candidato = $result_candidatos->fetch_assoc()): ?>
                <div class="card">
                    <input type="radio" id="candidato_<?php echo $candidato['id']; ?>" name="candidato" value="<?php echo $candidato['id']; ?>" required>
                    <label for="candidato_<?php echo $candidato['id']; ?>" class="label">
                        <?php echo htmlspecialchars($candidato['nombre']); ?> - <?php echo htmlspecialchars($candidato['cargo']); ?>
                    </label>
                    <img src="<?php echo htmlspecialchars($candidato['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($candidato['nombre']); ?>" class="candidate-photo">
                    <p><?php echo htmlspecialchars($candidato['propuestas']); ?></p>
                </div>
            <?php endwhile; ?>

            <h3>Actividades Recreativas</h3>
            <?php while ($actividad = $result_actividades->fetch_assoc()): ?>
                <div class="card">
                    <input type="radio" id="actividad_<?php echo $actividad['id']; ?>" name="actividad" value="<?php echo $actividad['id']; ?>" required>
                    <label for="actividad_<?php echo $actividad['id']; ?>" class="label">
                        <?php echo htmlspecialchars($actividad['nombre']); ?> - <?php echo htmlspecialchars($actividad['fecha']); ?>
                    </label>
                    <p><?php echo htmlspecialchars($actividad['descripcion']); ?></p>
                </div>
            <?php endwhile; ?>

            <button type="submit" class="button">Votar</button>
        </form>
        <p><a href="foro.php" class="link">Volver al Foro</a></p>
    </div>
</body>
</html>
