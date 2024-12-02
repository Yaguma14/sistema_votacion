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

// Verificar si el estudiante ya ha votado por todos los cargos y actividades
$usuario_id = $_SESSION['usuario_id'];
$sql_check_voto = "SELECT 
    (SELECT COUNT(*) FROM votos_candidatos WHERE usuario_id = $usuario_id) as votos_candidatos,
    (SELECT COUNT(*) FROM votos_actividades WHERE usuario_id = $usuario_id) as votos_actividades";
$result_check_voto = $conn->query($sql_check_voto);
$votos = $result_check_voto->fetch_assoc();

if ($votos['votos_candidatos'] == 3 && $votos['votos_actividades'] > 0) {
    // El estudiante ya ha votado por todo, redirigir al foro
    header("Location: foro.php?mensaje=ya_votaste");
    exit();
}

// Definir las etapas de votación
$etapas = ['personero', 'cavildante', 'contralor', 'actividades'];
$etapa_actual = isset($_GET['etapa']) ? $_GET['etapa'] : 'personero';

if (!in_array($etapa_actual, $etapas)) {
    $etapa_actual = 'personero';
}

// Procesar el voto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $voto = $conn->real_escape_string($_POST['voto']);
    
    if ($etapa_actual == 'actividades') {
        $sql = "INSERT INTO votos_actividades (usuario_id, actividad_id) VALUES ('$usuario_id', '$voto')";
    } else {
        $sql = "INSERT INTO votos_candidatos (usuario_id, candidato_id) VALUES ('$usuario_id', '$voto')";
    }
    
    if ($conn->query($sql) === TRUE) {
        $etapa_index = array_search($etapa_actual, $etapas);
        if ($etapa_index !== false && $etapa_index < count($etapas) - 1) {
            $siguiente_etapa = $etapas[$etapa_index + 1];
            header("Location: votaciones.php?etapa=$siguiente_etapa");
            exit();
        } else {
            header("Location: foro.php?mensaje=voto_exitoso");
            exit();
        }
    } else {
        $error = "Error al registrar el voto: " . $conn->error;
    }
}

// Obtener opciones de voto según la etapa actual
if ($etapa_actual == 'actividades') {
    $sql = "SELECT * FROM actividades_recreativas";
} else {
    $sql = "SELECT * FROM candidatos WHERE cargo = '$etapa_actual'";
}
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votaciones - <?php echo ucfirst($etapa_actual); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Votaciones - <?php echo ucfirst($etapa_actual); ?></h2>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div>
                <input type="radio" id="opcion_<?php echo $row['id']; ?>" name="voto" value="<?php echo $row['id']; ?>" required>
                <label for="opcion_<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['nombre']); ?>
                    <?php if ($etapa_actual != 'actividades'): ?>
                        - <?php echo htmlspecialchars($row['cargo']); ?>
                    <?php endif; ?>
                </label>
                <?php if ($etapa_actual != 'actividades' && isset($row['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>" width="100">
                <?php endif; ?>
                <p><?php echo htmlspecialchars($row['propuestas'] ?? $row['descripcion']); ?></p>
            </div>
        <?php endwhile; ?>

        <button type="submit">Votar y Continuar</button>
    </form>
    <p><a href="foro.php">Cancelar y volver al Foro</a></p>
</body>
</html>