<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Aquí irá la lógica para gestionar usuarios, candidatos, actividades, etc.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="./css/admin_panel.css">
    <link rel="icon" type="image/x-icon" href="./img/icono.jpg">
</head>
<body>
    <!-- Video de fondo -->
    <video autoplay muted loop id="background-video">
        <source src="./video/panel2.mp4" type="video/mp4">
        Tu navegador no soporta la reproducción de videos.
    </video>

    <center>
        <div class="content">
        <h2>Panel de Administrador</h2>
    <nav>
        <ul>
            <li><a href="gestionar_usuarios.php">Gestionar Usuarios</a></li>
            <li><a href="gestionar_candidatos.php">Gestionar Candidatos</a></li>
            <li><a href="gestionar_actividades.php">Gestionar Actividades Recreativas</a></li>
            <li><a href="gestionar_votaciones.php">Gestionar Votaciones</a></li>
            <li><a href="publicar_novedad.php">Publicar Novedad</a></li>
        </ul>
    </nav>
    <p><a href="logout.php" class="logout-link">Cerrar Sesión</a></p>
        </div>
    </center>
</body>
</html>