<?php
// Iniciar sesión con configuración robusta
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

// ── CREDENCIALES (cámbialas aquí) ──
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'sena2026');

// Si ya está autenticado, redirigir directo al panel
if (isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true) {
    header("Location: admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = trim($_POST['clave'] ?? '');

    if ($usuario === ADMIN_USER && $clave === ADMIN_PASS) {
        $_SESSION['admin_auth'] = true;
        $_SESSION['admin_user'] = $usuario;
        header("Location: admin.php");
        exit();
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin — Iniciar sesión</title>
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
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
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
        }

        body::after {
            content: '';
            position: fixed;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(123,92,250,0.1) 0%, transparent 70%);
            top: -120px; right: -120px;
            pointer-events: none;
        }

        .orb2 {
            position: fixed;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,245,196,0.07) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            pointer-events: none;
        }

        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            margin: 20px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 36px 32px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.5);
            animation: floatIn 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes floatIn {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .top-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 30px;
        }

        .dot { width: 10px; height: 10px; border-radius: 50%; }
        .dot-r { background: #ff4d6d; }
        .dot-y { background: #ffd166; }
        .dot-g { background: var(--accent); }

        .top-bar-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
            margin-left: auto;
            letter-spacing: 1px;
        }

        .icon-wrap {
            width: 56px; height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(123,92,250,0.2), rgba(0,245,196,0.1));
            border: 1px solid rgba(0,245,196,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 22px;
            box-shadow: 0 0 24px rgba(0,245,196,0.08);
        }

        .tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        h2 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, var(--text) 30%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .input-wrap { margin-bottom: 14px; }

        .input-wrap label {
            display: block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 16px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s;
        }

        .input-wrap input::placeholder { color: var(--muted); font-size: 14px; }

        .input-wrap input:focus {
            border-color: rgba(0,245,196,0.5);
            box-shadow: 0 0 0 3px rgba(0,245,196,0.07);
        }

        .error-msg {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,77,109,0.1);
            border: 1px solid rgba(255,77,109,0.25);
            border-radius: 8px;
            padding: 10px 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--danger);
            margin-bottom: 16px;
            animation: shake 0.35s ease;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-6px); }
            75%      { transform: translateX(6px); }
        }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
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
            box-shadow: 0 4px 20px rgba(0,245,196,0.2);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,245,196,0.35);
        }

        button[type="submit"]:active { transform: translateY(0); }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--muted);
        }

        .footer span { color: var(--accent); }
    </style>
</head>
<body>

<div class="orb2"></div>

<div class="card">

    <div class="top-bar">
        <div class="dot dot-r"></div>
        <div class="dot dot-y"></div>
        <div class="dot dot-g"></div>
        <span class="top-bar-label">admin_login.php</span>
    </div>

    <div class="icon-wrap">🛡️</div>

    <div class="tag">// acceso restringido</div>
    <h2>Panel de Admin</h2>
    <p class="subtitle">Solo personal autorizado.<br>Ingresa tus credenciales para continuar.</p>

    <?php if ($error): ?>
        <div class="error-msg">⚠ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-wrap">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario"
                   placeholder="Ej: admin"
                   value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                   required autocomplete="off">
        </div>
        <div class="input-wrap">
            <label for="clave">Contraseña</label>
            <input type="password" id="clave" name="clave"
                   placeholder="••••••••"
                   required>
        </div>
        <button type="submit">Ingresar al panel →</button>
    </form>

    <div class="footer">
        Sistema de Evaluación Web &nbsp;·&nbsp; <span>© 2026</span>
    </div>

</div>

</body>
</html>
