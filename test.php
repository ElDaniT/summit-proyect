<?php
include 'config/db.php';

if ($conn) {
    echo '✅ Conexión exitosa a la base de datos!';
} else {
    echo '❌ Error al conectar';
}
?>