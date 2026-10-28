<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_registro'])) $_SESSION['csrf_registro'] = bin2hex(random_bytes(32));
// Se mantiene la configuración horaria de la página de registro.
date_default_timezone_set('America/Argentina/Buenos_Aires');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cuenta - LunarMC Network</title>
   <link rel="stylesheet" href="index-.css?v=20261025">
</head>
<body class="dark-mode">

<header class="header-top">

    <div class="header-left">
        <button
            id="copy-ip-btn"
            class="clickable-ip"
            type="button"
            onclick="copyIP()"
        >
            IP: lunarmc.cc (Haz clic para copiar)
        </button>
    </div>

    <div class="header-center">
        <h1 class="header-title">
            Tienda Minecraft Oficial de
            <?php echo htmlspecialchars(defined('APP_NAME') ? APP_NAME : 'LunarMC Network', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
        </h1>
    </div>

    <div class="header-right">

        <a href="registro.php" class="btn-register-link">
            <button type="button" class="btn-register">
                Regístrate 🔑
            </button>
        </a>

        <button
            type="button"
            id="toggle-theme-btn"
        >
            ☀️ Modo Claro
        </button>

    </div>

</header>

    <!-- ==========================================
         FECHA Y HORA
         ========================================== -->
    <div
        class="datetime-banner"
        id="datetime-banner"
    >
        Fecha: <?php echo date("d/m/Y"); ?> |
        Hora actual:
        <span id="clock">
            <?php echo date("H:i:s"); ?>
        </span>
    </div>


    <!-- ==========================================
         HERO / BANNER CENTRAL
         ========================================== -->
    <section class="header-container text-center">

        <div class="banner-box">

            <img
                src="imagen del servidor lunar.png"
                alt="LunarMC Banner"
                class="banner-img"
            >

        </div>


        <!-- Barra de Navegación -->
        <nav class="main-nav">
            <div class="dropdown">
                <button type="button" class="dropdown-btn">
                    Tridentbox <span class="arrow">▼</span>
                </button>
                <div class="dropdown-menu">
                    <a href="rangos.php" class="dropdown-item">RANGOS</a>
                    <a href="extras.php" class="dropdown-item">EXTRAS</a>
                </div>
            </div>

            <a href="index.php" class="nav-link">Inicio</a>
            <a href="Acerca de Nosotros.php" class="nav-link">Acerca de Nosotros</a>
        </nav>

        <section style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #F1C40F; font-size: 1.8em; font-weight: bold; margin-bottom: 10px;">
                Registro de Cuenta - LunarMC Network
            </h2>
            <p style="color: #E1E1E6; font-size: 0.95em;">
                Crea tu cuenta para acceder a la tienda y guardar tus preferencias.
            </p>
        </section>

        <form id="register-form" class="register-card" action="procesar_registro.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_registro'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <label for="username" class="form-label">Nombre de usuario (Nick en Minecraft):</label>
                <input type="text" id="username" name="username" class="input-field" placeholder="Tu_Nick_123" required>
            </div>

            <div class="form-group">
                <label for="tipo_cuenta" class="form-label">Tipo de cuenta de Minecraft:</label>
                <select id="tipo_cuenta" name="tipo_cuenta" class="input-field select-field" required>
                    <option value="" disabled selected>Seleccioná el tipo de cuenta</option>
                    <option value="premium">Prémium (Minecraft Java original)</option>
                    <option value="no_premium">No prémium (launcher no oficial)</option>
                </select>
                <small style="display:block;color:#bfc8df;margin-top:7px">Prémium: se buscará la skin pública de tu nick. No prémium: se mostrará Steve. Esta selección no verifica que seas dueño de la cuenta.</small>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Correo electrónico:</label>
                <input type="email" id="email" name="email" class="input-field" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" id="password" name="password" class="input-field" placeholder="Mínimo 8 caracteres" minlength="8" required>
            </div>

            <div class="form-group">
                <label for="gender" class="form-label">Sexo:</label>
                <select id="gender" name="gender" class="input-field select-field" required>
                    <option value="" disabled selected>Selecciona una opción</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit-register">Completar Registro 🔑</button>
                <a href="registro.php" class="btn-cancel-link">[Cancelar X]</a>
            </div>
        </form>
    </main>

    <script src="index-.js"></script>
</body>
</html>

