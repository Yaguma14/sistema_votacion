<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_votaciones');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "";
}

session_start();

function verificar_sesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}

function es_superadministrador() {
    // Verifica si el usuario ha iniciado sesión y si su rol es 'superadmin'
    return isset($_SESSION['usuario_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] == 'superadmin';
}

// Agrega una contraseña para acceder a la página de registro de administradores
define('ADMIN_REGISTER_PASSWORD', '101420');


function es_administrador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador';
}

function registrar_log($usuario_id, $accion, $detalles = '') {
    global $conn;
    $usuario_id = $usuario_id ? $conn->real_escape_string($usuario_id) : 'NULL';
    $accion = $conn->real_escape_string($accion);
    $detalles = $conn->real_escape_string($detalles);
    
    $sql = "INSERT INTO logs (usuario_id, accion, detalles) VALUES ($usuario_id, '$accion', '$detalles')";
    $conn->query($sql);
}

function validar_nombre($nombre) {
    return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/', $nombre);
}

function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validar_contrasena($contrasena) {
    return strlen($contrasena) >= 8;
}

function validar_cargo($cargo) {
    $cargos_validos = ['personero', 'cavildante', 'contralor'];
    return in_array($cargo, $cargos_validos);
}

function paginar($sql, $pagina = 1, $por_pagina = 10) {
    global $conn;
    
    $inicio = ($pagina - 1) * $por_pagina;
    
    $sql_count = "SELECT COUNT(*) as total FROM (" . $sql . ") as subquery";
    $result_count = $conn->query($sql_count);
    $total = $result_count->fetch_assoc()['total'];
    
    $total_paginas = ceil($total / $por_pagina);
    
    $sql_paginado = $sql . " LIMIT $inicio, $por_pagina";
    $result = $conn->query($sql_paginado);
    
    return [
        'resultado' => $result,
        'total_paginas' => $total_paginas,
        'pagina_actual' => $pagina
    ];
}

function crear_notificacion($usuario_id, $mensaje, $tipo = 'general') {
    global $conn;
    $usuario_id = $usuario_id ? $conn->real_escape_string($usuario_id) : 'NULL';
    $mensaje = $conn->real_escape_string($mensaje);
    $tipo = $conn->real_escape_string($tipo);
    
    $sql = "INSERT INTO notificaciones (usuario_id, mensaje, tipo) VALUES ($usuario_id, '$mensaje', '$tipo')";
    $conn->query($sql);
}

function crear_notificacion_global($mensaje, $tipo = 'general') {
    global $conn;
    $sql = "INSERT INTO notificaciones (usuario_id, mensaje, tipo) 
            SELECT id, '$mensaje', '$tipo' FROM usuarios WHERE rol = 'estudiante'";
    $conn->query($sql);
}

function obtener_notificaciones($usuario_id) {
    global $conn;
    $usuario_id = $conn->real_escape_string($usuario_id);
    
    $sql = "SELECT * FROM notificaciones WHERE usuario_id = $usuario_id ORDER BY fecha_creacion DESC LIMIT 5";
    return $conn->query($sql);
}

function marcar_notificacion_leida($notificacion_id) {
    global $conn;
    $notificacion_id = $conn->real_escape_string($notificacion_id);
    
    $sql = "UPDATE notificaciones SET leida = TRUE WHERE id = $notificacion_id";
    $conn->query($sql);
}

function buscar($tabla, $campos, $termino, $condiciones_adicionales = '') {
    global $conn;
    $termino = $conn->real_escape_string($termino);
    $condiciones = [];
    foreach ($campos as $campo) {
        $condiciones[] = "$campo LIKE '%$termino%'";
    }
    $condicion = implode(' OR ', $condiciones);
    if ($condiciones_adicionales) {
        $condicion = "($condicion) AND $condiciones_adicionales";
    }
    $sql = "SELECT * FROM $tabla WHERE $condicion";
    return $conn->query($sql);
}
?>