<?php
// ══════════════════════════════════════════════════════
//  INSTALADOR DE BASE DE DATOS  —  evaluacion_web
//  Abre este archivo UNA sola vez en el navegador:
//  http://localhost/tu-carpeta/instalar_bd.php
//  Luego BÓRRALO por seguridad.
// ══════════════════════════════════════════════════════

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');       // ← pon tu contraseña si tienes

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("<p style='color:red;font-family:monospace'>❌ No se pudo conectar a MySQL: " . $conn->connect_error . "</p>");
}

$pasos = [];

// 1. Crear base de datos
$r = $conn->query("CREATE DATABASE IF NOT EXISTS `evaluacion_web` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$pasos[] = $r ? "✅ Base de datos <b>evaluacion_web</b> creada (o ya existía)" : "❌ Error creando BD: " . $conn->error;

// 2. Seleccionar BD
$conn->select_db("evaluacion_web");

// 3. Tabla estudiantes
$r = $conn->query("CREATE TABLE IF NOT EXISTS `estudiantes` (
    `id`                  INT(11) NOT NULL AUTO_INCREMENT,
    `nombre`              VARCHAR(100) NOT NULL,
    `conectado`           TINYINT(1) DEFAULT 1,
    `completado`          TINYINT(1) DEFAULT 0,
    `hora_conexion`       TIMESTAMP NULL DEFAULT NULL,
    `hora_desconexion`    TIMESTAMP NULL DEFAULT NULL,
    `tiempo_desconectado` INT(11) DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$pasos[] = $r ? "✅ Tabla <b>estudiantes</b> lista" : "❌ Error en tabla estudiantes: " . $conn->error;

// 4. Tabla respuestas
$r = $conn->query("CREATE TABLE IF NOT EXISTS `respuestas` (
    `id`            INT(11) NOT NULL AUTO_INCREMENT,
    `estudiante_id` INT(11) DEFAULT NULL,
    `pregunta`      VARCHAR(255) DEFAULT NULL,
    `respuesta`     VARCHAR(255) DEFAULT NULL,
    `fecha`         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `estudiante_id` (`estudiante_id`),
    CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$pasos[] = $r ? "✅ Tabla <b>respuestas</b> lista" : "❌ Error en tabla respuestas: " . $conn->error;

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Instalador BD</title>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<style>
    :root{--bg:#0d0f1a;--card:#181c30;--border:#252a45;--accent:#00f5c4;--accent2:#7b5cfa;--text:#e2e8f8;--muted:#6b7499;}
    body{font-family:'Syne',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;}
    .card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:40px;max-width:500px;width:100%;margin:20px;box-shadow:0 24px 64px rgba(0,0,0,0.5);}
    h2{font-size:22px;font-weight:800;background:linear-gradient(90deg,var(--text),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:24px;}
    .step{font-family:'JetBrains Mono',monospace;font-size:13px;padding:10px 14px;border-radius:8px;margin-bottom:10px;background:rgba(255,255,255,0.03);border:1px solid var(--border);}
    .warn{margin-top:24px;padding:14px;background:rgba(255,209,102,0.08);border:1px solid rgba(255,209,102,0.3);border-radius:10px;font-family:'JetBrains Mono',monospace;font-size:12px;color:#ffd166;}
    .btn{display:inline-block;margin-top:20px;padding:12px 24px;background:linear-gradient(135deg,var(--accent2),var(--accent));color:#0d0f1a;font-weight:800;text-decoration:none;border-radius:10px;font-size:14px;}
</style>
</head>
<body>
<div class="card">
    <h2>🛠 Instalador de Base de Datos</h2>
    <?php foreach ($pasos as $p): ?>
        <div class="step"><?php echo $p; ?></div>
    <?php endforeach; ?>
    <div class="warn">
        ⚠ <b>¡Importante!</b> Borra o renombra este archivo (<code>instalar_bd.php</code>) después de usarlo para evitar que alguien lo ejecute de nuevo.
    </div>
    <a href="admin_login.php" class="btn">Ir al panel admin →</a>
</div>
</body>
</html>
