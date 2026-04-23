<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $tiempo = intval($_POST["tiempo"]);

    $sql = "UPDATE estudiantes 
            SET tiempo_desconectado = tiempo_desconectado + ?, 
                hora_desconexion = NOW() 
            WHERE nombre=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("is", $tiempo, $nombre);
    $stmt->execute();
}
?>
