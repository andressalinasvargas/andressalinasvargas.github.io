<?php
include("db.php");

if (isset($_GET["nombre"])) {
    $nombre = $_GET["nombre"];

    // Usar consulta preparada para mayor seguridad
    $sql = "UPDATE estudiantes SET conectado = FALSE, hora_desconexion = NOW() WHERE nombre = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sesión cerrada</title>
    <style>
        body { font-family: Arial; text-align: center; margin-top: 50px; background: #f9f9f9; }
        .mensaje { padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <div class="mensaje">
        <h2>Has cerrado sesión, <?php echo htmlspecialchars($nombre); ?>.</h2>
        <p>Tu desconexión ha sido registrada en el sistema.</p>
    </div>
</body>
</html>
