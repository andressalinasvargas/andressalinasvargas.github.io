<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);

    if (!empty($nombre)) {
        $sql = "SELECT id, hora_desconexion FROM estudiantes WHERE nombre = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $tiempo_desconectado = 0;

            if (!empty($row["hora_desconexion"])) {
                $tiempo_desconectado = time() - strtotime($row["hora_desconexion"]);
            }

            $sql_update = "UPDATE estudiantes 
                           SET conectado = TRUE, hora_conexion = NOW(), 
                               tiempo_desconectado = tiempo_desconectado + ? 
                           WHERE id = ?";
            $stmt_update = $conexion->prepare($sql_update);
            $stmt_update->bind_param("ii", $tiempo_desconectado, $row["id"]);
            $stmt_update->execute();
        } else {
            $sql_insert = "INSERT INTO estudiantes (nombre, conectado, hora_conexion) 
                           VALUES (?, TRUE, NOW())";
            $stmt_insert = $conexion->prepare($sql_insert);
            $stmt_insert->bind_param("s", $nombre);
            $stmt_insert->execute();
        }

        header("Location: evaluacion.php?nombre=" . urlencode($nombre));
        exit();
    } else {
        echo "El nombre no puede estar vacío.";
    }
}
?>
