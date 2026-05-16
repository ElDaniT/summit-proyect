<?php
$host = 'localhost';
$puerto ='3307';
$usuario = 'root';
$password = '';
$base_de_datos = 'users_db';

$conn = new mysqli($host, $usuario, $password, $base_de_datos, $puerto);

if($conn->connect_error){
    die("Error al conectar a la base de datos" . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

?>