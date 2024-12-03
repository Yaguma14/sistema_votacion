<?php
$host = "localhost";
$usuario = "root"; // Cambia esto si tienes un usuario diferente
$contraseña = ""; // Cambia esto si tienes una contraseña
$base_datos = "sistema_votaciones";

$conexion = new mysqli($host, $usuario, $contraseña, $base_datos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
echo "Conexión exitosa a la base de datos";
?>
