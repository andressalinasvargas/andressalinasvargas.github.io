<?php
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];

    // Buscar ID del estudiante
    $sql = "SELECT id FROM estudiantes WHERE nombre=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $estudiante_id = $row["id"];

    // Respuestas correctas (25 preguntas, se toman 20 aleatorias)
    $respuestas_correctas = [
        // PHP
        "p1"  => "int",           "p2"  => "string",          "p3"  => "gettype()",
        "p20" => "//",            "p21" => '$variable',       "p22" => "echo",
        // HTML
        "p4"  => "img",           "p5"  => "a",               "p6"  => "style",
        "p16" => "ol",            "p17" => "body",            "p23" => "input",
        // CSS
        "p7"  => "background-color","p8" => "font-size",      "p9"  => "p",
        "p10" => "text-align",    "p18" => "padding",         "p24" => "flex",
        // JavaScript
        "p11" => "alert()",       "p12" => "getElementById()","p13" => "onclick",
        "p14" => "let",           "p15" => "for",             "p19" => "push()",
        "p25" => "===",
    ];

    $puntaje = 0;
    $total_preguntas = 0;

    // Guardar respuestas y calcular puntaje
    foreach ($_POST as $pregunta => $respuesta) {
        if ($pregunta != "nombre") {
            $total_preguntas++;
            if (empty($respuesta)) {
                $respuesta = "sin respuesta";
            }
            $sql_insert = "INSERT INTO respuestas (estudiante_id, pregunta, respuesta) VALUES (?, ?, ?)";
            $stmt_insert = $conexion->prepare($sql_insert);
            $stmt_insert->bind_param("iss", $estudiante_id, $pregunta, $respuesta);
            $stmt_insert->execute();

            if (isset($respuestas_correctas[$pregunta]) && $respuesta == $respuestas_correctas[$pregunta]) {
                $puntaje++;
            }
        }
    }

    // Marcar como completado
    $sql_update = "UPDATE estudiantes SET completado = TRUE WHERE id = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("i", $estudiante_id);
    $stmt_update->execute();

    $aprobado = ($total_preguntas > 0) && ($puntaje / $total_preguntas >= 0.6);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado Evaluación</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0d0f1a; --card: #181c30; --border: #252a45;
            --accent: #00f5c4; --accent2: #7b5cfa;
            --danger: #ff4d6d; --text: #e2e8f8; --muted: #6b7499;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg); color: var(--text);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        body::before {
            content: ''; position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(0,245,196,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,245,196,0.03) 1px, transparent 1px);
            background-size: 40px 40px; pointer-events: none;
        }
        .card {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px; margin: 20px;
            background: var(--card); border: 1px solid var(--border);
            border-radius: 20px; padding: 40px 36px 32px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
            text-align: center;
            animation: floatIn 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .icon { font-size: 48px; margin-bottom: 16px; }
        .tag {
            font-family: 'JetBrains Mono', monospace; font-size: 11px;
            letter-spacing: 3px; text-transform: uppercase; margin-bottom: 8px;
        }
        .tag.ok  { color: var(--accent); }
        .tag.fail { color: var(--danger); }
        h2 {
            font-size: 26px; font-weight: 800; letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        .nombre { color: var(--accent); }
        .score-big {
            font-family: 'JetBrains Mono', monospace;
            font-size: 48px; font-weight: 700;
            margin: 20px 0 6px;
            background: linear-gradient(90deg, var(--accent2), var(--accent));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .score-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px; color: var(--muted);
        }
        .bar-track {
            width: 100%; height: 8px; background: var(--border);
            border-radius: 99px; margin: 20px 0; overflow: hidden;
        }
        .bar-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, var(--accent2), var(--accent));
            transition: width 1s ease;
        }
        .home-btn {
            display: inline-block; margin-top: 20px;
            padding: 12px 28px;
            background: linear-gradient(135deg, var(--accent2), var(--accent));
            color: #0d0f1a; font-family: 'Syne', sans-serif;
            font-size: 14px; font-weight: 800; text-decoration: none;
            border-radius: 10px; letter-spacing: 1px; text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="card">
    <?php
    $pct = $total_preguntas > 0 ? round(($puntaje / $total_preguntas) * 100) : 0;
    if ($aprobado): ?>
        <div class="icon">🎉</div>
        <div class="tag ok">// evaluación completada</div>
        <h2>¡Felicitaciones, <span class="nombre"><?php echo htmlspecialchars($nombre); ?></span>!</h2>
        <p style="color:var(--muted);font-size:14px;margin-top:6px;">Has superado la evaluación con éxito.</p>
    <?php else: ?>
        <div class="icon">📋</div>
        <div class="tag fail">// evaluación completada</div>
        <h2>Gracias, <span class="nombre"><?php echo htmlspecialchars($nombre); ?></span>.</h2>
        <p style="color:var(--muted);font-size:14px;margin-top:6px;">No alcanzaste el puntaje mínimo esta vez.</p>
    <?php endif; ?>

    <div class="score-big"><?php echo $puntaje; ?>/<?php echo $total_preguntas; ?></div>
    <div class="score-label"><?php echo $pct; ?>% de respuestas correctas &nbsp;·&nbsp; mínimo 60%</div>

    <div class="bar-track">
        <div class="bar-fill" id="bar" style="width:0%"></div>
    </div>

    <a href="index.html" class="home-btn">Volver al inicio →</a>
</div>
<script>
    setTimeout(() => {
        document.getElementById('bar').style.width = '<?php echo $pct; ?>%';
    }, 200);
</script>
</body>
</html>
