<?php
session_start();
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include("db.php");

// ── SOLO ADMIN: protege con sesión si tienes login de admin ──
// session_start(); if (!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit(); }

// ── VALIDAR ID ──
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de estudiante inválido.");
}
$estudiante_id = intval($_GET['id']);

// ── DATOS DEL ESTUDIANTE ──
$sql_est = "SELECT * FROM estudiantes WHERE id = ?";
$stmt = $conexion->prepare($sql_est);
$stmt->bind_param("i", $estudiante_id);
$stmt->execute();
$est = $stmt->get_result()->fetch_assoc();

if (!$est) {
    die("Estudiante no encontrado.");
}

// ── TEXTOS Y RESPUESTAS CORRECTAS ──
$preguntas_info = [
    "p1"  => ["texto" => "¿Cuál es el tipo de dato para números enteros en PHP?",        "correcta" => "int",              "categoria" => "PHP"],
    "p2"  => ["texto" => "¿Qué tipo de dato se usa para texto en PHP?",                  "correcta" => "string",           "categoria" => "PHP"],
    "p3"  => ["texto" => "¿Qué función devuelve el tipo de una variable en PHP?",        "correcta" => "gettype()",        "categoria" => "PHP"],
    "p20" => ["texto" => "¿Qué símbolo se usa para comentar una línea en PHP?",          "correcta" => "//",               "categoria" => "PHP"],
    "p21" => ["texto" => "¿Cómo se declara una variable en PHP?",                        "correcta" => "\$variable",       "categoria" => "PHP"],
    "p22" => ["texto" => "¿Qué función imprime texto en PHP?",                           "correcta" => "echo",             "categoria" => "PHP"],
    "p4"  => ["texto" => "¿Qué etiqueta HTML se usa para insertar una imagen?",          "correcta" => "img",              "categoria" => "HTML"],
    "p5"  => ["texto" => "¿Qué etiqueta HTML se usa para crear un enlace?",              "correcta" => "a",                "categoria" => "HTML"],
    "p6"  => ["texto" => "¿Qué atributo HTML se usa para dar estilo en línea?",          "correcta" => "style",            "categoria" => "HTML"],
    "p16" => ["texto" => "¿Qué etiqueta HTML se usa para crear una lista ordenada?",     "correcta" => "ol",               "categoria" => "HTML"],
    "p17" => ["texto" => "¿Qué etiqueta HTML define el cuerpo del documento?",           "correcta" => "body",             "categoria" => "HTML"],
    "p23" => ["texto" => "¿Qué etiqueta HTML crea un campo de texto en un formulario?",  "correcta" => "input",            "categoria" => "HTML"],
    "p7"  => ["texto" => "¿Qué propiedad CSS cambia el color de fondo?",                 "correcta" => "background-color", "categoria" => "CSS"],
    "p8"  => ["texto" => "¿Qué propiedad CSS cambia el tamaño de la fuente?",            "correcta" => "font-size",        "categoria" => "CSS"],
    "p9"  => ["texto" => "¿Qué selector CSS aplica estilo a todos los párrafos?",        "correcta" => "p",                "categoria" => "CSS"],
    "p10" => ["texto" => "¿Qué propiedad CSS centra un texto?",                          "correcta" => "text-align",       "categoria" => "CSS"],
    "p18" => ["texto" => "¿Qué propiedad CSS controla el espacio interno de un elemento?","correcta" => "padding",          "categoria" => "CSS"],
    "p24" => ["texto" => "¿Qué valor de display convierte un elemento en bloque flexible?","correcta" => "flex",            "categoria" => "CSS"],
    "p11" => ["texto" => "¿Qué función JS muestra un mensaje emergente?",                "correcta" => "alert()",          "categoria" => "JS"],
    "p12" => ["texto" => "¿Qué método JS selecciona un elemento por ID?",                "correcta" => "getElementById()", "categoria" => "JS"],
    "p13" => ["texto" => "¿Qué evento JS se dispara al hacer clic?",                     "correcta" => "onclick",          "categoria" => "JS"],
    "p14" => ["texto" => "¿Qué palabra clave JS declara una variable de bloque?",        "correcta" => "let",              "categoria" => "JS"],
    "p15" => ["texto" => "¿Qué estructura JS se usa para repetir código?",               "correcta" => "for",              "categoria" => "JS"],
    "p19" => ["texto" => "¿Qué método JS añade un elemento al final de un array?",       "correcta" => "push()",           "categoria" => "JS"],
    "p25" => ["texto" => "¿Qué operador JS compara valor y tipo a la vez?",              "correcta" => "===",              "categoria" => "JS"],
];

// ── RESPUESTAS DEL ESTUDIANTE (última por pregunta) ──
$sql_resp = "SELECT pregunta, respuesta, fecha 
             FROM respuestas 
             WHERE estudiante_id = ? 
             ORDER BY fecha DESC";
$stmt2 = $conexion->prepare($sql_resp);
$stmt2->bind_param("i", $estudiante_id);
$stmt2->execute();
$res2 = $stmt2->get_result();

$respuestas_alumno = [];
while ($r = $res2->fetch_assoc()) {
    // Guardar solo la primera (más reciente) por pregunta
    if (!isset($respuestas_alumno[$r['pregunta']])) {
        $respuestas_alumno[$r['pregunta']] = $r;
    }
}

// ── CALCULAR PUNTAJE REAL ──
$aciertos = 0;
$total_respondidas = count($respuestas_alumno);
foreach ($respuestas_alumno as $pregunta => $resp) {
    if (isset($preguntas_info[$pregunta]) && $resp['respuesta'] === $preguntas_info[$pregunta]['correcta']) {
        $aciertos++;
    }
}

// ── AGRUPAR POR CATEGORÍA ──
$categorias = ['PHP' => [], 'HTML' => [], 'CSS' => [], 'JS' => []];
foreach ($respuestas_alumno as $clave => $resp) {
    $info = $preguntas_info[$clave] ?? null;
    if ($info) {
        $categorias[$info['categoria']][] = [
            'clave'     => $clave,
            'texto'     => $info['texto'],
            'correcta'  => $info['correcta'],
            'dada'      => $resp['respuesta'],
            'es_correcta' => $resp['respuesta'] === $info['correcta'],
            'fecha'     => $resp['fecha'],
        ];
    }
}

$pct = $total_respondidas > 0 ? round(($aciertos / $total_respondidas) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respuestas — <?php echo htmlspecialchars($est['nombre']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:      #0d0f1a;
            --surface: #13162a;
            --card:    #181c30;
            --border:  #252a45;
            --accent:  #00f5c4;
            --accent2: #7b5cfa;
            --danger:  #ff4d6d;
            --warn:    #ffd166;
            --text:    #e2e8f8;
            --muted:   #6b7499;
            --green:   #00f5c4;
            --red:     #ff4d6d;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding-bottom: 60px;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,245,196,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,245,196,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── HEADER ── */
        header {
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, #0d0f1a 0%, #13162a 100%);
            border-bottom: 1px solid var(--border);
            padding: 24px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .header-left .tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        header h1 {
            font-size: clamp(18px, 3vw, 26px);
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, var(--text) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 16px;
            text-decoration: none;
            transition: color 0.2s, border-color 0.2s;
        }

        .back-btn:hover { color: var(--accent); border-color: rgba(0,245,196,0.3); }

        /* ── SCORE SUMMARY ── */
        .summary {
            position: relative;
            z-index: 1;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 24px 40px;
            display: flex;
            align-items: center;
            gap: 32px;
            flex-wrap: wrap;
        }

        .avatar-lg {
            width: 56px; height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(123,92,250,0.25), rgba(0,245,196,0.15));
            border: 1px solid rgba(0,245,196,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            color: var(--accent);
            flex-shrink: 0;
        }

        .student-info h2 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 4px;
        }

        .student-info p {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 1px;
        }

        .score-summary {
            margin-left: auto;
            text-align: right;
        }

        .score-big {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -2px;
            background: linear-gradient(90deg, var(--accent2), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }

        .score-sub {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .score-bar-wrap {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-end;
        }

        .score-bar {
            width: 140px;
            height: 6px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
        }

        .score-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent2), var(--accent));
            border-radius: 99px;
            box-shadow: 0 0 8px rgba(0,245,196,0.4);
        }

        .pct-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--accent);
            min-width: 36px;
        }

        /* ── STATS CHIPS ── */
        .chips {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
            padding: 16px 40px;
            border-bottom: 1px solid var(--border);
            background: var(--card);
        }

        .chip {
            display: flex;
            align-items: center;
            gap: 7px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            padding: 6px 14px;
            border-radius: 99px;
            border: 1px solid;
        }

        .chip-correct { color: var(--green); background: rgba(0,245,196,0.08); border-color: rgba(0,245,196,0.2); }
        .chip-wrong   { color: var(--red);   background: rgba(255,77,109,0.08); border-color: rgba(255,77,109,0.2); }
        .chip-total   { color: var(--muted); background: rgba(107,116,153,0.08); border-color: rgba(107,116,153,0.2); }

        /* ── CONTAINER ── */
        .container {
            position: relative;
            z-index: 1;
            max-width: 960px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        /* ── CATEGORIA HEADER ── */
        .cat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 32px 0 16px;
        }

        .cat-header:first-child { margin-top: 0; }

        .cat-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 4px;
        }

        .cat-PHP  { color: #7b5cfa; background: rgba(123,92,250,0.12); border: 1px solid rgba(123,92,250,0.25); }
        .cat-HTML { color: #ff8c42; background: rgba(255,140,66,0.10); border: 1px solid rgba(255,140,66,0.25); }
        .cat-CSS  { color: #00bfff; background: rgba(0,191,255,0.10);  border: 1px solid rgba(0,191,255,0.25); }
        .cat-JS   { color: #ffd166; background: rgba(255,209,102,0.10); border: 1px solid rgba(255,209,102,0.25); }

        .cat-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .cat-score {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
        }

        /* ── PREGUNTA CARD ── */
        .q-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 10px;
            display: grid;
            grid-template-columns: 28px 1fr auto;
            gap: 14px;
            align-items: start;
            transition: border-color 0.2s;
            animation: slideIn 0.4s ease both;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .q-card.correcta {
            border-left: 3px solid var(--green);
        }

        .q-card.incorrecta {
            border-left: 3px solid var(--red);
        }

        .q-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .q-icon.ok  { background: rgba(0,245,196,0.12); color: var(--green); }
        .q-icon.bad { background: rgba(255,77,109,0.12); color: var(--red); }

        .q-body {}

        .q-texto {
            font-size: 14px;
            color: var(--text);
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .q-respuestas {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .resp-pill {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            padding: 4px 12px;
            border-radius: 99px;
            border: 1px solid;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .resp-pill.dada-correcta {
            color: var(--green);
            background: rgba(0,245,196,0.1);
            border-color: rgba(0,245,196,0.3);
        }

        .resp-pill.dada-incorrecta {
            color: var(--red);
            background: rgba(255,77,109,0.1);
            border-color: rgba(255,77,109,0.3);
            text-decoration: line-through;
            opacity: 0.8;
        }

        .resp-pill.correcta-label {
            color: var(--green);
            background: transparent;
            border-color: rgba(0,245,196,0.2);
        }

        .resp-meta {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: var(--muted);
            white-space: nowrap;
            margin-top: 4px;
        }

        /* ── EMPTY ── */
        .empty-cat {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--muted);
            padding: 16px 22px;
            background: var(--card);
            border: 1px dashed var(--border);
            border-radius: 10px;
            text-align: center;
            margin-bottom: 10px;
        }

        /* ── FOOTER ── */
        .footer {
            text-align: center;
            margin-top: 32px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
        }

        .footer span { color: var(--accent); }

        @media (max-width: 640px) {
            header, .summary, .chips { padding: 16px; }
            .container { padding: 16px 12px; }
            .q-card { grid-template-columns: 24px 1fr; }
            .resp-meta { display: none; }
            .score-summary { margin-left: 0; width: 100%; text-align: left; }
            .score-bar-wrap { justify-content: flex-start; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="header-left">
        <div class="tag">// admin · respuestas</div>
        <h1>Detalle de Evaluación</h1>
    </div>
    <a href="admin.php" class="back-btn">← Volver al panel</a>
</header>

<!-- RESUMEN DEL ESTUDIANTE -->
<div class="summary">
    <div class="avatar-lg"><?php echo strtoupper(mb_substr($est['nombre'], 0, 1)); ?></div>
    <div class="student-info">
        <h2><?php echo htmlspecialchars($est['nombre']); ?></h2>
        <p>
            <?php echo $est['completado'] ? '✓ COMPLETÓ EL EXAMEN' : '⏳ NO COMPLETADO'; ?>
            &nbsp;·&nbsp;
            Conectado: <?php echo $est['hora_conexion'] ?? '—'; ?>
        </p>
    </div>
    <div class="score-summary">
        <div class="score-big"><?php echo $aciertos; ?><span style="font-size:22px;color:var(--muted);">/<?php echo $total_respondidas; ?></span></div>
        <div class="score-sub">PREGUNTAS CORRECTAS</div>
        <div class="score-bar-wrap">
            <div class="score-bar">
                <div class="score-bar-fill" style="width:<?php echo $pct; ?>%"></div>
            </div>
            <span class="pct-label"><?php echo $pct; ?>%</span>
        </div>
    </div>
</div>

<!-- CHIPS ──────────────────────────────────── -->
<div class="chips">
    <span class="chip chip-correct">✓ <?php echo $aciertos; ?> correctas</span>
    <span class="chip chip-wrong">✗ <?php echo ($total_respondidas - $aciertos); ?> incorrectas</span>
    <span class="chip chip-total"><?php echo $total_respondidas; ?> respondidas</span>
    <?php
    foreach ($categorias as $cat => $pregs) {
        if (empty($pregs)) continue;
        $ok = array_sum(array_column($pregs, 'es_correcta'));
        $tot = count($pregs);
        echo "<span class='chip chip-total'>$cat: $ok/$tot</span>";
    }
    ?>
</div>

<!-- PREGUNTAS ──────────────────────────────── -->
<div class="container">

    <?php foreach ($categorias as $cat => $pregs): ?>

    <?php
        $ok_cat  = array_sum(array_column($pregs, 'es_correcta'));
        $tot_cat = count($pregs);
    ?>

    <div class="cat-header">
        <span class="cat-label cat-<?php echo $cat; ?>"><?php echo $cat; ?></span>
        <div class="cat-line"></div>
        <span class="cat-score"><?php echo $ok_cat; ?>/<?php echo $tot_cat; ?></span>
    </div>

    <?php if (empty($pregs)): ?>
        <div class="empty-cat">Sin preguntas de esta categoría respondidas</div>
    <?php else: ?>
        <?php foreach ($pregs as $idx => $q): ?>
        <div class="q-card <?php echo $q['es_correcta'] ? 'correcta' : 'incorrecta'; ?>"
             style="animation-delay: <?php echo ($idx * 0.05); ?>s">

            <!-- Ícono -->
            <div class="q-icon <?php echo $q['es_correcta'] ? 'ok' : 'bad'; ?>">
                <?php echo $q['es_correcta'] ? '✓' : '✗'; ?>
            </div>

            <!-- Cuerpo -->
            <div class="q-body">
                <div class="q-texto"><?php echo htmlspecialchars($q['texto']); ?></div>
                <div class="q-respuestas">
                    <?php if ($q['es_correcta']): ?>
                        <span class="resp-pill dada-correcta">✓ <?php echo htmlspecialchars($q['dada']); ?></span>
                    <?php else: ?>
                        <span class="resp-pill dada-incorrecta">✗ <?php echo htmlspecialchars($q['dada']); ?></span>
                        <span class="resp-pill correcta-label">→ <?php echo htmlspecialchars($q['correcta']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Fecha -->
            <div class="resp-meta"><?php echo $q['fecha']; ?></div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php endforeach; ?>

    <p class="footer">
        Sistema de Evaluación Web &nbsp;·&nbsp; <span>Vista Admin</span> &nbsp;·&nbsp; © 2026
    </p>
</div>

</body>
</html>
<?php $conexion->close(); ?>
