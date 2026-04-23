<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include("db.php");

$respuestas_correctas = [
    "p1"  => "int",           "p2"  => "string",          "p3"  => "gettype()",
    "p20" => "//",            "p21" => '$variable',       "p22" => "echo",
    "p4"  => "img",           "p5"  => "a",               "p6"  => "style",
    "p16" => "ol",            "p17" => "body",            "p23" => "input",
    "p7"  => "background-color","p8" => "font-size",      "p9"  => "p",
    "p10" => "text-align",    "p18" => "padding",         "p24" => "flex",
    "p11" => "alert()",       "p12" => "getElementById()","p13" => "onclick",
    "p14" => "let",           "p15" => "for",             "p19" => "push()",
    "p25" => "===",
];

$sql = "SELECT * FROM estudiantes ORDER BY hora_conexion ASC";
$resultado = $conexion->query($sql);

$total = 0; $online = 0; $completados = 0; $offline = 0; $filas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $filas[] = $fila;
        $total++;
        if ($fila['conectado'] == 1)  $online++;
        if ($fila['completado'] == 1) $completados++;
        if ($fila['conectado'] == 0)  $offline++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:#0d0f1a;--surface:#13162a;--card:#181c30;--border:#252a45;
            --accent:#00f5c4;--accent2:#7b5cfa;--danger:#ff4d6d;--warn:#ffd166;
            --text:#e2e8f8;--muted:#6b7499;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Syne',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;padding-bottom:60px;overflow-x:hidden;}
        body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(0,245,196,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,245,196,0.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;z-index:0;}
        header{position:relative;z-index:1;background:linear-gradient(135deg,#0d0f1a 0%,#13162a 100%);border-bottom:1px solid var(--border);padding:24px 40px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;}
        .header-left .tag{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--accent);letter-spacing:3px;text-transform:uppercase;margin-bottom:5px;}
        header h1{font-size:clamp(20px,3vw,30px);font-weight:800;letter-spacing:-1px;background:linear-gradient(90deg,var(--text) 0%,var(--accent) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .header-right{display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
        .timestamp{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--muted);}
        .live-badge{display:flex;align-items:center;gap:8px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--accent);letter-spacing:1px;}
        .live-dot{width:8px;height:8px;background:var(--accent);border-radius:50%;box-shadow:0 0 8px var(--accent);animation:pulse 1.8s ease-in-out infinite;}
        @keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(0.8);}}
        .stats-bar{position:relative;z-index:1;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;padding:24px 40px;background:var(--surface);border-bottom:1px solid var(--border);}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px 20px;position:relative;overflow:hidden;}
        .stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;}
        .stat-card.s-total::after{background:var(--text);}
        .stat-card.s-online::after{background:var(--accent);box-shadow:0 0 8px var(--accent);}
        .stat-card.s-done::after{background:var(--accent2);}
        .stat-card.s-off::after{background:var(--danger);}
        .stat-label{font-family:'JetBrains Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;}
        .stat-value{font-size:32px;font-weight:800;letter-spacing:-1px;}
        .s-total .stat-value{color:var(--text);}.s-online .stat-value{color:var(--accent);}.s-done .stat-value{color:var(--accent2);}.s-off .stat-value{color:var(--danger);}
        .container{position:relative;z-index:1;max-width:1300px;margin:0 auto;padding:32px 24px;}
        .table-wrap{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;overflow-x:auto;}
        .table-header{display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid var(--border);gap:12px;flex-wrap:wrap;}
        .table-title{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;}
        .refresh-btn{display:flex;align-items:center;gap:6px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--accent);background:rgba(0,245,196,0.08);border:1px solid rgba(0,245,196,0.2);border-radius:6px;padding:6px 14px;cursor:pointer;text-decoration:none;transition:background 0.2s;}
        .refresh-btn:hover{background:rgba(0,245,196,0.15);}
        table{width:100%;border-collapse:collapse;min-width:820px;}
        thead tr{background:var(--surface);border-bottom:1px solid var(--border);}
        th{font-family:'JetBrains Mono',monospace;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--muted);padding:13px 18px;text-align:left;white-space:nowrap;}
        tbody tr{border-bottom:1px solid var(--border);transition:background 0.15s;}
        tbody tr:last-child{border-bottom:none;}
        tbody tr:hover{background:rgba(255,255,255,0.015);}
        td{padding:13px 18px;font-size:13px;color:var(--text);vertical-align:middle;}
        .td-name{display:flex;align-items:center;gap:10px;font-weight:700;font-size:14px;white-space:nowrap;}
        .avatar{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--accent2),var(--accent));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#0d0f1a;flex-shrink:0;}
        .badge{display:inline-flex;align-items:center;gap:5px;font-family:'JetBrains Mono',monospace;font-size:10px;font-weight:700;padding:4px 10px;border-radius:99px;white-space:nowrap;}
        .badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0;}
        .badge.online{background:rgba(0,245,196,0.1);color:var(--accent);border:1px solid rgba(0,245,196,0.25);}
        .badge.online::before{background:var(--accent);box-shadow:0 0 5px var(--accent);animation:pulse 1.8s ease-in-out infinite;}
        .badge.offline{background:rgba(255,77,109,0.1);color:var(--danger);border:1px solid rgba(255,77,109,0.25);}
        .badge.offline::before{background:var(--danger);}
        .badge.listo{background:rgba(123,92,250,0.1);color:var(--accent2);border:1px solid rgba(123,92,250,0.25);}
        .badge.listo::before{background:var(--accent2);}
        .badge.pendiente{background:rgba(255,209,102,0.1);color:var(--warn);border:1px solid rgba(255,209,102,0.25);}
        .badge.pendiente::before{background:var(--warn);}
        .td-mono{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--muted);white-space:nowrap;}
        .td-alert{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--warn);font-weight:700;}
        .score-wrap{display:flex;align-items:center;gap:10px;min-width:130px;}
        .score-track{flex:1;height:6px;background:var(--border);border-radius:99px;overflow:hidden;}
        .score-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--accent2),var(--accent));}
        .score-text{font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--text);white-space:nowrap;min-width:40px;text-align:right;}
        .empty{padding:60px 20px;text-align:center;}
        .empty-icon{font-size:36px;margin-bottom:12px;}
        .empty p{font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--muted);}
        .footer{text-align:center;margin-top:28px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--muted);}
        .footer span{color:var(--accent);}
        @media(max-width:640px){header,.stats-bar{padding:16px;}.container{padding:16px 10px;}}
    </style>
</head>
<body>

<header>
    <div class="header-left">
        <div class="tag">// admin panel</div>
        <h1>Estado de Estudiantes</h1>
    </div>
    <div class="header-right">
        <span class="timestamp"><?php echo date('d/m/Y H:i:s'); ?></span>
        <div class="live-badge"><div class="live-dot"></div>EN VIVO</div>
        <a href="admin_logout.php" style="font-family:'JetBrains Mono',monospace;font-size:11px;color:#ff4d6d;background:rgba(255,77,109,0.08);border:1px solid rgba(255,77,109,0.2);border-radius:6px;padding:6px 14px;text-decoration:none;transition:background 0.2s;"
           onmouseover="this.style.background='rgba(255,77,109,0.18)'"
           onmouseout="this.style.background='rgba(255,77,109,0.08)'">⏻ Salir</a>
    </div>
</header>

<div class="stats-bar">
    <div class="stat-card s-total"><div class="stat-label">Total</div><div class="stat-value"><?php echo $total; ?></div></div>
    <div class="stat-card s-online"><div class="stat-label">Conectados</div><div class="stat-value"><?php echo $online; ?></div></div>
    <div class="stat-card s-done"><div class="stat-label">Completados</div><div class="stat-value"><?php echo $completados; ?></div></div>
    <div class="stat-card s-off"><div class="stat-label">Desconectados</div><div class="stat-value"><?php echo $offline; ?></div></div>
</div>

<div class="container">
    <div class="table-wrap">
        <div class="table-header">
            <span class="table-title">// <?php echo $total; ?> estudiante<?php echo $total != 1 ? 's' : ''; ?> registrado<?php echo $total != 1 ? 's' : ''; ?></span>
            <a href="" class="refresh-btn">↻ Actualizar</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Estudiante</th><th>Conexión</th><th>Estado</th>
                    <th>Hora Conexión</th><th>Hora Desconexión</th><th>T. Desconectado</th>
                    <th>Puntaje</th><th>Respuestas</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($filas)): ?>
                <tr><td colspan="9"><div class="empty"><div class="empty-icon">📋</div><p>Ningún estudiante registrado aún.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($filas as $i => $f):
                    $inicial    = strtoupper(mb_substr($f['nombre'], 0, 1));
                    $conectado  = $f['conectado'] == 1;
                    $completado = $f['completado'] == 1;
                    $tiempoDesc = intval($f['tiempo_desconectado'] ?? 0);
                    $puntaje = 0;
                    $sql_pts = "SELECT pregunta, respuesta FROM respuestas WHERE estudiante_id = ? ORDER BY fecha DESC";
                    $stmt_pts = $conexion->prepare($sql_pts);
                    $stmt_pts->bind_param("i", $f['id']);
                    $stmt_pts->execute();
                    $res_pts = $stmt_pts->get_result();
                    $vistas = [];
                    while ($rp = $res_pts->fetch_assoc()) {
                        if (!isset($vistas[$rp['pregunta']])) {
                            $vistas[$rp['pregunta']] = true;
                            if (isset($respuestas_correctas[$rp['pregunta']]) && $rp['respuesta'] === $respuestas_correctas[$rp['pregunta']]) {
                                $puntaje++;
                            }
                        }
                    }
                    $respondidas = count($vistas);
                    $pct = $respondidas > 0 ? round(($puntaje / $respondidas) * 100) : 0;
                    $horaConn = !empty($f['hora_conexion'])    ? $f['hora_conexion']    : '—';
                    $horaDisc = !empty($f['hora_desconexion']) ? $f['hora_desconexion'] : '—';
                ?>
                <tr>
                    <td class="td-mono"><?php echo $i + 1; ?></td>
                    <td><div class="td-name"><div class="avatar"><?php echo $inicial; ?></div><?php echo htmlspecialchars($f['nombre']); ?></div></td>
                    <td><span class="badge <?php echo $conectado ? 'online' : 'offline'; ?>"><?php echo $conectado ? 'Online' : 'Offline'; ?></span></td>
                    <td><span class="badge <?php echo $completado ? 'listo' : 'pendiente'; ?>"><?php echo $completado ? 'Completado' : 'Pendiente'; ?></span></td>
                    <td class="td-mono"><?php echo $horaConn; ?></td>
                    <td class="td-mono"><?php echo $horaDisc; ?></td>
                    <td class="<?php echo $tiempoDesc > 60 ? 'td-alert' : 'td-mono'; ?>">
                        <?php
                        if ($tiempoDesc > 0) {
                            $min = floor($tiempoDesc / 60); $seg = $tiempoDesc % 60;
                            echo $min > 0 ? "{$min}m {$seg}s" : "{$seg}s";
                        } else { echo '—'; }
                        ?>
                    </td>
                    <td>
                        <?php if ($completado): ?>
                        <div class="score-wrap">
                            <div class="score-track"><div class="score-fill" style="width:<?php echo $pct; ?>%"></div></div>
                            <span class="score-text"><?php echo $puntaje; ?>/<?php echo $respondidas; ?></span>
                        </div>
                        <?php else: ?><span class="td-mono">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($respondidas > 0): ?>
                        <a href="ver_respuestas.php?id=<?php echo $f['id']; ?>" style="display:inline-flex;align-items:center;gap:6px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--accent2);background:rgba(123,92,250,0.1);border:1px solid rgba(123,92,250,0.25);border-radius:6px;padding:6px 12px;text-decoration:none;transition:background 0.2s;"
                           onmouseover="this.style.background='rgba(123,92,250,0.2)'"
                           onmouseout="this.style.background='rgba(123,92,250,0.1)'">🔍 Ver</a>
                        <?php else: ?><span class="td-mono">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="footer">Sistema de Evaluación Web &nbsp;·&nbsp; <span>© 2026</span> &nbsp;·&nbsp; Auto-refresh en <span id="cd">30</span>s</p>
</div>

<script>
    let s = 30;
    const cd = document.getElementById('cd');
    setInterval(() => { s--; if (cd) cd.textContent = s; if (s <= 0) location.reload(); }, 1000);
</script>
</body>
</html>
<?php $conexion->close(); ?>
