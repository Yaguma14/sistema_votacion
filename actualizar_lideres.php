<?php
require_once 'config.php';

$sql = "SELECT c.id, c.nombre, c.cargo, COUNT(v.id) as votos
        FROM candidatos c
        LEFT JOIN votos_candidatos v ON c.id = v.candidato_id
        GROUP BY c.id
        ORDER BY c.cargo, votos DESC";

$result = $conn->query($sql);

$lideres = [];
$cargos_procesados = [];

while ($row = $result->fetch_assoc()) {
    if (!in_array($row['cargo'], $cargos_procesados)) {
        $lideres[] = $row;
        $cargos_procesados[] = $row['cargo'];
    }
}

foreach ($lideres as $lider) {
    $mensaje = "Actualización de votaciones: {$lider['nombre']} lidera como {$lider['cargo']} con {$lider['votos']} votos.";
    crear_notificacion_global($mensaje, "lider");
}