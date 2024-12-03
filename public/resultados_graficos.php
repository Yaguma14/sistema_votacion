<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Obtener resultados de votaciones
$sql_personeros = "SELECT c.nombre, COUNT(v.id) as votos 
                   FROM candidatos c 
                   LEFT JOIN votos_candidatos v ON c.id = v.candidato_id 
                   WHERE c.cargo = 'personero' 
                   GROUP BY c.id";
$result_personeros = $conn->query($sql_personeros);

$sql_cavildantes = "SELECT c.nombre, COUNT(v.id) as votos 
                    FROM candidatos c 
                    LEFT JOIN votos_candidatos v ON c.id = v.candidato_id 
                    WHERE c.cargo = 'cavildante' 
                    GROUP BY c.id";
$result_cavildantes = $conn->query($sql_cavildantes);

$sql_contralores = "SELECT c.nombre, COUNT(v.id) as votos 
                    FROM candidatos c 
                    LEFT JOIN votos_candidatos v ON c.id = v.candidato_id 
                    WHERE c.cargo = 'contralor' 
                    GROUP BY c.id";
$result_contralores = $conn->query($sql_contralores);

$sql_actividades = "SELECT a.nombre, COUNT(v.id) as votos 
                    FROM actividades_recreativas a 
                    LEFT JOIN votos_actividades v ON a.id = v.actividad_id 
                    GROUP BY a.id";
$result_actividades = $conn->query($sql_actividades);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Votaciones</title>
    <link rel="stylesheet" href="css/resultados_graficas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Video de fondo -->
    <div class="background-video">
        <video autoplay muted loop>
            <source src="video/graficas.mp4" type="video/mp4">
            Tu navegador no soporta videos HTML5.
        </video>
    </div>

    <!-- Contenido principal -->
    <div class="container">
        <h2>Resultados de Votaciones</h2>

        <div class="chart-container">
            <canvas id="personeros"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="cavildantes"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="contralores"></canvas>
        </div>
        <div class="chart-container">
            <canvas id="actividades"></canvas>
        </div>

        <p><a href="admin_panel.php" class="link">Volver al Panel de Administrador</a></p>
    </div>

    <script>
        function crearGrafico(id, etiqueta, labels, data) {
            new Chart(document.getElementById(id), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: etiqueta,
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        <?php
        function imprimirDatosGrafico($result) {
            $labels = [];
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['nombre'];
                $data[] = $row['votos'];
            }
            echo "labels: " . json_encode($labels) . ", ";
            echo "data: " . json_encode($data);
        }
        ?>

        crearGrafico('personeros', 'Votos para Personeros', {<?php imprimirDatosGrafico($result_personeros); ?>});
        crearGrafico('cavildantes', 'Votos para Cavildantes', {<?php imprimirDatosGrafico($result_cavildantes); ?>});
        crearGrafico('contralores', 'Votos para Contralores', {<?php imprimirDatosGrafico($result_contralores); ?>});
        crearGrafico('actividades', 'Votos para Actividades Recreativas', {<?php imprimirDatosGrafico($result_actividades); ?>});
    </script>
</body>
</html>
