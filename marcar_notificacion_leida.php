<?php
require_once 'config.php';
verificar_sesion();

$usuario_id = $_SESSION['usuario_id'];
$notificacion_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($notificacion_id > 0) {
    $sql = "UPDATE notificaciones SET leida = 1 WHERE id = $notificacion_id AND usuario_id = $usuario_id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID de notificación inválido']);
}