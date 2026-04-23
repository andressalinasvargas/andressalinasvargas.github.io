<?php
include("db.php");
$nombre = $_GET["nombre"];

// ── BANCO DE PREGUNTAS ──
// Cada pregunta: [name, texto, opciones[], respuesta_correcta]
$preguntas = [
    // PHP
    ["p1",  "¿Cuál es el tipo de dato para números enteros en PHP?",
        ["int", "float", "string"], "int"],
    ["p2",  "¿Qué tipo de dato se usa para texto en PHP?",
        ["string", "char", "text"], "string"],
    ["p3",  "¿Qué función devuelve el tipo de una variable en PHP?",
        ["gettype()", "typeof()", "var_dump()"], "gettype()"],
    ["p20", "¿Qué símbolo se usa para comentar una línea en PHP?",
        ["//", "##", "/* */"], "//"],
    ["p21", "¿Cómo se declara una variable en PHP?",
        ["\$variable", "var variable", "let variable"], "\$variable"],
    ["p22", "¿Qué función imprime texto en PHP?",
        ["echo", "print_text()", "console.log()"], "echo"],

    // HTML
    ["p4",  "¿Qué etiqueta HTML se usa para insertar una imagen?",
        ["&lt;img&gt;", "&lt;image&gt;", "&lt;picture&gt;"], "img"],
    ["p5",  "¿Qué etiqueta HTML se usa para crear un enlace?",
        ["&lt;a&gt;", "&lt;link&gt;", "&lt;href&gt;"], "a"],
    ["p6",  "¿Qué atributo HTML se usa para dar estilo en línea?",
        ["style", "class", "id"], "style"],
    ["p16", "¿Qué etiqueta HTML se usa para crear una lista ordenada?",
        ["&lt;ol&gt;", "&lt;ul&gt;", "&lt;li&gt;"], "ol"],
    ["p17", "¿Qué etiqueta HTML define el cuerpo del documento?",
        ["&lt;body&gt;", "&lt;main&gt;", "&lt;section&gt;"], "body"],
    ["p23", "¿Qué etiqueta HTML crea un campo de texto en un formulario?",
        ["&lt;input type='text'&gt;", "&lt;textfield&gt;", "&lt;field&gt;"], "input"],

    // CSS
    ["p7",  "¿Qué propiedad CSS cambia el color de fondo?",
        ["background-color", "color", "fill"], "background-color"],
    ["p8",  "¿Qué propiedad CSS cambia el tamaño de la fuente?",
        ["font-size", "text-size", "size"], "font-size"],
    ["p9",  "¿Qué selector CSS aplica estilo a todos los párrafos?",
        ["p { }", ".p { }", "#p { }"], "p"],
    ["p10", "¿Qué propiedad CSS centra un texto?",
        ["text-align", "align", "center"], "text-align"],
    ["p18", "¿Qué propiedad CSS controla el espacio interno de un elemento?",
        ["padding", "margin", "border"], "padding"],
    ["p24", "¿Qué valor de display convierte un elemento en bloque flexible?",
        ["flex", "block", "inline"], "flex"],

    // JavaScript
    ["p11", "¿Qué función JS muestra un mensaje emergente?",
        ["alert()", "prompt()", "confirm()"], "alert()"],
    ["p12", "¿Qué método JS selecciona un elemento por ID?",
        ["document.getElementById()", "document.querySelector()", "document.getElement()"], "getElementById()"],
    ["p13", "¿Qué evento JS se dispara al hacer clic?",
        ["onclick", "onhover", "onpress"], "onclick"],
    ["p14", "¿Qué palabra clave JS declara una variable de bloque?",
        ["let", "var", "set"], "let"],
    ["p15", "¿Qué estructura JS se usa para repetir código?",
        ["for", "loop", "repeat"], "for"],
    ["p19", "¿Qué método JS añade un elemento al final de un array?",
        ["push()", "pop()", "append()"], "push()"],
    ["p25", "¿Qué operador JS compara valor y tipo a la vez?",
        ["===", "==", "="], "==="],
];

// ── MEZCLAR preguntas y opciones aleatoriamente ──
shuffle($preguntas);
foreach ($preguntas as &$p) {
    $displayOpts = $p[2];
    shuffle($displayOpts);
    $p[2] = $displayOpts;
}
unset($p);

// Tomar solo 20 preguntas
$preguntas = array_slice($preguntas, 0, 20);
$total = count($preguntas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluación Integral Web</title>
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
            --text:    #e2e8f8;
            --muted:   #6b7499;
            --glow:    0 0 20px rgba(0,245,196,0.18);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 0 0 60px;
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
            padding: 28px 40px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .header-left .tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        header h1 {
            font-size: clamp(22px, 3vw, 32px);
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(90deg, var(--text) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .student-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 8px 18px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--accent);
            white-space: nowrap;
        }

        .student-badge::before { content: '▸'; color: var(--accent2); }

        /* ── PROGRESS ── */
        .progress-wrap {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 10px 40px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .progress-track {
            flex: 1;
            height: 4px;
            background: var(--border);
            border-radius: 99px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--accent2), var(--accent));
            border-radius: 99px;
            transition: width 0.4s ease;
            box-shadow: 0 0 10px rgba(0,245,196,0.5);
        }

        .progress-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--muted);
            min-width: 60px;
            text-align: right;
        }

        /* ── LAYOUT ── */
        .container {
            position: relative;
            z-index: 1;
            max-width: 860px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* ── SECTION LABEL ── */
        .section-label {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 40px 0 16px;
        }

        .section-label span {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--muted);
        }

        .section-label::before {
            content: '';
            display: block;
            width: 28px;
            height: 2px;
            background: var(--accent);
            border-radius: 2px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── QUESTION CARD ── */
        .pregunta {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 22px 24px;
            margin-bottom: 14px;
            transition: border-color 0.25s, box-shadow 0.25s;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.4s ease both;
        }

        .pregunta::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--border);
            border-radius: 3px 0 0 3px;
            transition: background 0.25s;
        }

        .pregunta.answered::before {
            background: linear-gradient(180deg, var(--accent2), var(--accent));
        }

        .pregunta:hover {
            border-color: rgba(0,245,196,0.3);
            box-shadow: var(--glow);
        }

        .q-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 16px;
        }

        .q-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 700;
            color: var(--accent);
            background: rgba(0,245,196,0.08);
            border: 1px solid rgba(0,245,196,0.2);
            border-radius: 6px;
            padding: 3px 8px;
            white-space: nowrap;
            margin-top: 2px;
        }

        .q-text {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.5;
        }

        /* ── OPTIONS ── */
        .options {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-left: 8px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }

        .option:hover {
            background: rgba(255,255,255,0.03);
            border-color: var(--border);
        }

        .option input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 2px solid var(--muted);
            border-radius: 50%;
            cursor: pointer;
            flex-shrink: 0;
            transition: border-color 0.2s, background 0.2s;
            position: relative;
        }

        .option input[type="radio"]:checked {
            border-color: var(--accent);
            background: var(--accent);
            box-shadow: 0 0 8px rgba(0,245,196,0.4);
        }

        .option input[type="radio"]:checked::after {
            content: '';
            position: absolute;
            width: 6px; height: 6px;
            background: var(--bg);
            border-radius: 50%;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        .option:has(input:checked) {
            background: rgba(0,245,196,0.06);
            border-color: rgba(0,245,196,0.25);
        }

        .option-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--text);
            cursor: pointer;
        }

        /* ── SUBMIT ── */
        .submit-wrap {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            align-items: center;
        }

        .unanswered-warning {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--danger);
            display: none;
        }

        button[type="submit"] {
            width: 100%;
            max-width: 400px;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--accent2) 0%, var(--accent) 100%);
            color: #0d0f1a;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 24px rgba(0,245,196,0.25);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,245,196,0.4);
        }

        button[type="submit"]:active { transform: translateY(0); }

        /* ── LOGOUT ── */
        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .logout-link:hover { color: var(--danger); }
        .logout-link::before { content: '⏻'; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            header { flex-direction: column; align-items: flex-start; padding: 20px; }
            .progress-wrap { padding: 10px 20px; }
            .container { padding: 24px 14px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="header-left">
            <div class="tag">// Evaluación activa</div>
            <h1>Programación Web</h1>
        </div>
        <div class="student-badge"><?php echo htmlspecialchars($nombre); ?></div>
    </header>

    <div class="progress-wrap">
        <div class="progress-track">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        <div class="progress-label" id="progressLabel">0 / <?php echo $total; ?></div>
    </div>

    <div class="container">
        <form action="finalizar.php" method="POST" id="evalForm">
            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">

            <?php
            $seccionActual = '';
            $seccionMap = [
                'p1'  => 'PHP — Tipos y funciones',
                'p2'  => 'PHP — Tipos y funciones',
                'p3'  => 'PHP — Tipos y funciones',
                'p20' => 'PHP — Tipos y funciones',
                'p21' => 'PHP — Tipos y funciones',
                'p22' => 'PHP — Tipos y funciones',
                'p4'  => 'HTML — Etiquetas y atributos',
                'p5'  => 'HTML — Etiquetas y atributos',
                'p6'  => 'HTML — Etiquetas y atributos',
                'p16' => 'HTML — Etiquetas y atributos',
                'p17' => 'HTML — Etiquetas y atributos',
                'p23' => 'HTML — Etiquetas y atributos',
                'p7'  => 'CSS — Propiedades y selectores',
                'p8'  => 'CSS — Propiedades y selectores',
                'p9'  => 'CSS — Propiedades y selectores',
                'p10' => 'CSS — Propiedades y selectores',
                'p18' => 'CSS — Propiedades y selectores',
                'p24' => 'CSS — Propiedades y selectores',
                'p11' => 'JavaScript — DOM y lógica',
                'p12' => 'JavaScript — DOM y lógica',
                'p13' => 'JavaScript — DOM y lógica',
                'p14' => 'JavaScript — DOM y lógica',
                'p15' => 'JavaScript — DOM y lógica',
                'p19' => 'JavaScript — DOM y lógica',
                'p25' => 'JavaScript — DOM y lógica',
            ];

            foreach ($preguntas as $i => $preg):
                $name    = $preg[0];
                $texto   = $preg[1];
                $opciones = $preg[2];
                $num     = str_pad($i + 1, 2, '0', STR_PAD_LEFT);

                // Mostrar etiqueta de sección si cambió
                $seccion = $seccionMap[$name] ?? '';
                if ($seccion && $seccion !== $seccionActual):
                    $seccionActual = $seccion;
            ?>
                <div class="section-label"><span><?php echo $seccion; ?></span></div>
            <?php endif; ?>

            <div class="pregunta" style="animation-delay: <?php echo ($i * 0.04); ?>s">
                <div class="q-header">
                    <span class="q-num">Q<?php echo $num; ?></span>
                    <span class="q-text"><?php echo $texto; ?></span>
                </div>
                <div class="options">
                    <?php foreach ($opciones as $opcion): ?>
                    <label class="option">
                        <input type="radio" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars(strip_tags($opcion)); ?>">
                        <span class="option-label"><?php echo $opcion; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php endforeach; ?>

            <div class="submit-wrap">
                <p class="unanswered-warning" id="warning">⚠ Hay preguntas sin responder</p>
                <button type="submit">Finalizar evaluación →</button>
            </div>
        </form>

        <a href="logout.php?nombre=<?php echo urlencode($nombre); ?>" class="logout-link">
            Salir / Desconectarse
        </a>
    </div>

    <script>
        const total = <?php echo $total; ?>;
        const fill  = document.getElementById('progressFill');
        const label = document.getElementById('progressLabel');

        function updateProgress() {
            // Cuenta preguntas únicas respondidas (por name)
            const answered = new Set(
                Array.from(document.querySelectorAll('input[type="radio"]:checked')).map(r => r.name)
            ).size;
            fill.style.width  = Math.round((answered / total) * 100) + '%';
            label.textContent = answered + ' / ' + total;
        }

        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function () {
                updateProgress();
                this.closest('.pregunta').classList.add('answered');
            });
        });

        document.getElementById('evalForm').addEventListener('submit', function(e) {
            const names = new Set(
                Array.from(document.querySelectorAll('input[type="radio"]')).map(r => r.name)
            );
            const answered = new Set(
                Array.from(document.querySelectorAll('input[type="radio"]:checked')).map(r => r.name)
            );
            const warning = document.getElementById('warning');
            if (answered.size < names.size) {
                e.preventDefault();
                warning.style.display = 'block';
                for (const name of names) {
                    if (!answered.has(name)) {
                        document.querySelector(`input[name="${name}"]`)
                            .closest('.pregunta')
                            .scrollIntoView({ behavior: 'smooth', block: 'center' });
                        break;
                    }
                }
            }
        });

        // Tracker de desconexión
        let inicioDesconexion = null;
        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                inicioDesconexion = new Date();
            } else {
                if (inicioDesconexion) {
                    const tiempo = Math.floor((new Date() - inicioDesconexion) / 1000);
                    fetch("registrar_desconexion.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "nombre=<?php echo urlencode($nombre); ?>&tiempo=" + tiempo
                    });
                    inicioDesconexion = null;
                }
            }
        });
    </script>
</body>
</html>