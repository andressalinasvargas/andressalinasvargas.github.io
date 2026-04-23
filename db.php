<?php
// ══════════════════════════════════════════════
//  CONFIGURACIÓN DE BASE DE DATOS
//  Cambia estos 4 valores según tu servidor
// ══════════════════════════════════════════════
define('DB_HOST', 'localhost');      // Servidor MySQL (casi siempre "localhost")
define('DB_USER', 'root');           // Usuario de MySQL
define('DB_PASS', '');               // Contraseña de MySQL (vacía en XAMPP por defecto)
define('DB_NAME', 'evaluacion_web'); // Nombre de la base de datos

$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conexion->connect_error) {
    die("<div style='font-family:monospace;background:#1a0010;color:#ff4d6d;padding:20px;border-radius:8px;margin:20px;'>
         <b>⚠ Error de conexión a la base de datos</b><br><br>
         " . htmlspecialchars($conexion->connect_error) . "<br><br>
         <small>Revisa los datos en <code>db.php</code>: host, usuario, contraseña y nombre de BD.</small>
         </div>");
}

$conexion->set_charset("utf8mb4");
?>
