<?php

require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| TOKEN CSRF
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token']) ||
    $_SESSION['csrf_token'] === ''
) {
    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

$csrf_token = $_SESSION['csrf_token'];


/*
|--------------------------------------------------------------------------
| USUARIO
|--------------------------------------------------------------------------
*/

$usuario_logueado = isset(
    $_SESSION['usuario_id']
);

// El panel y el menú usan la misma regla de cuenta premium desde MySQL.
$esAdministrador = false;
$skinPremiumMenu = false;

if ($usuario_logueado) {
    require_once __DIR__ . '/database.php';

    try {
        $stmtRol = $pdo->prepare(
            "SELECT rol_id, tipo_cuenta FROM usuarios WHERE id = :id LIMIT 1"
        );
        $stmtRol->execute([':id' => (int) $_SESSION['usuario_id']]);
        $datosMenu = $stmtRol->fetch(PDO::FETCH_ASSOC);
        $esAdministrador = $datosMenu && (int) $datosMenu['rol_id'] === 2;
        $skinPremiumMenu = $datosMenu
            && ($datosMenu['tipo_cuenta'] ?? '') === 'premium'
            && preg_match('/^[A-Za-z0-9_]{3,16}$/D', (string) ($_SESSION['nick'] ?? '')) === 1;
    } catch (PDOException $e) {
        error_log('LunarMC cabecera: ' . $e->getMessage());
    }
}

$nick_usuario = $usuario_logueado
    ? ($_SESSION['nick'] ?? 'Usuario')
    : '';

// Mostrar el acceso al panel solo si MySQL confirma el rol administrador.
$es_admin_menu = false;
if ($usuario_logueado) {
    try {
        require_once __DIR__ . '/database.php';
        $consulta_rol_menu = $pdo->prepare('SELECT rol_id FROM usuarios WHERE id = :id LIMIT 1');
        $consulta_rol_menu->execute([':id' => (int) $_SESSION['usuario_id']]);
        $es_admin_menu = (int) $consulta_rol_menu->fetchColumn() === 2;
    } catch (PDOException $e) {
        error_log('LunarMC menu admin: ' . $e->getMessage());
    }
}

$es_inicio = basename(
    $_SERVER['PHP_SELF'] ?? ''
) === 'index.php';


/*
|--------------------------------------------------------------------------
| RENDER 3D DEL JUGADOR
|--------------------------------------------------------------------------
*/

$skin_usuario = '';

if (
    $usuario_logueado &&
    $nick_usuario !== ''
) {

    $skin_usuario =
        'https://mc-heads.net/body/' .
        rawurlencode($nick_usuario) .
        '/100';

}


/*
|--------------------------------------------------------------------------
| TOTAL DEL CARRITO
|--------------------------------------------------------------------------
*/

$total_carrito_usd = 0;

if (
    $usuario_logueado &&
    !empty($_SESSION['carrito']) &&
    is_array($_SESSION['carrito'])
) {

    foreach (
        $_SESSION['carrito']
        as $producto
    ) {

        $precio = (float) (
            $producto['precio'] ?? 0
        );

        $cantidad = (int) (
            $producto['cantidad'] ?? 0
        );

        $total_carrito_usd +=
            $precio * $cantidad;

    }

}

?>


<!-- ==========================================================
     TOKEN CSRF PARA JAVASCRIPT
     ========================================================== -->

<input
    type="hidden"
    id="lunarmc-csrf-token"
    value="<?php echo htmlspecialchars(
        $csrf_token,
        ENT_QUOTES,
        'UTF-8'
    ); ?>"
>


<!-- ==========================================================
     HEADER PRINCIPAL
     ========================================================== -->

<header class="header-top">


    <!-- ======================================================
         IZQUIERDA
         ====================================================== -->

    <div class="header-left">

        <button
            id="copy-ip-btn"
            class="clickable-ip"
            type="button"
        >
            IP: lunarmc.cc
        </button>

    </div>


    <!-- ======================================================
         CENTRO
         ====================================================== -->

    <div class="header-center">

        <h1 class="header-title">

            <?php

            echo defined('APP_NAME')
                ? htmlspecialchars(
                    APP_NAME,
                    ENT_QUOTES,
                    'UTF-8'
                )
                : 'LunarMC Network';

            ?>

        </h1>

    </div>


    <!-- ======================================================
         DERECHA
         ====================================================== -->

    <div class="header-right">


        <?php if ($usuario_logueado): ?>


            <!-- ==================================================
                 BOTÓN DE CUENTA
                 ================================================== -->

            <button
                type="button"
                id="open-account-menu"
                class="account-user"
                aria-label="Abrir menú de cuenta"
                aria-expanded="false"
            >


                <span class="account-skin-wrapper lunar-avatar-top">
                    <img class="lunar-avatar-fallback lunar-avatar-head-fallback"
                         src="https://mc-heads.net/body/<?php echo rawurlencode($nick_usuario); ?>/100"
                         alt="Skin de <?php echo htmlspecialchars($nick_usuario, ENT_QUOTES, 'UTF-8'); ?>"
                         width="64" height="64" loading="eager" referrerpolicy="no-referrer">
                    <canvas class="lunar-head-avatar" data-lunarmc-head-avatar
                            width="64" height="64" aria-label="Cabeza 3D del jugador" hidden></canvas>
                </span>


                <span class="account-user-info">

                    <span class="account-user-label">
    <?php echo !empty($esAdministrador) ? 'ADMINISTRADOR' : 'MI CUENTA'; ?>
</span>


                    <span class="account-user-name">

                        <?php

                        echo htmlspecialchars(
                            $nick_usuario,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        ?>

                    </span>

                </span>


                <span class="account-arrow">
                    ▾
                </span>


            </button>


            <!-- ==================================================
                 MENÚ DESPLEGABLE DE CUENTA
                 ================================================== -->

            <div
                id="account-menu-overlay"
                class="account-menu-overlay"
                aria-hidden="true"
            >


                <div
                    class="account-menu"
                    role="dialog"
                    aria-label="Menú de cuenta"
                >


                    <!-- ==========================================
                         CABECERA DEL MENÚ
                         ========================================== -->

                    <div class="account-menu-header">


                        <div class="account-menu-profile">


                            <div class="account-menu-skin-wrapper lunar-avatar-menu">
                                <img class="lunar-avatar-fallback lunar-avatar-body-fallback"
                                     src="https://mc-heads.net/body/<?php echo rawurlencode($nick_usuario); ?>/100"
                                     alt="Skin de <?php echo htmlspecialchars($nick_usuario, ENT_QUOTES, 'UTF-8'); ?>"
                                     width="64" height="64" loading="eager" referrerpolicy="no-referrer">
                                <canvas class="lunar-menu-skin-canvas" data-lunarmc-menu-skin
                                        width="128" height="128" aria-label="Personaje 3D del jugador" hidden></canvas>
                            </div>


                            <div class="account-menu-user-info">

                                <span class="account-menu-label">
                                    CUENTA
                                </span>


                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $nick_usuario,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>

                                </strong>

                            </div>

                        </div>


                        <button
                            type="button"
                            id="close-account-menu"
                            class="account-menu-close"
                            aria-label="Cerrar menú"
                        >
                            ×
                        </button>


                    </div>


                    <!-- ==========================================
                         CUERPO DEL MENÚ
                         ========================================== -->

                    <div class="account-menu-body">


                        <!-- CARRITO -->

                        <a
                            href="carrito.php"
                            class="account-menu-item"
                        >

                            <span class="account-menu-item-icon">
                                🛒
                            </span>


                            <span class="account-menu-item-text">

                                <strong>
                                    Carrito
                                </strong>

                                <small>
                                    Revisar mis productos
                                </small>

                            </span>


                            <span class="account-menu-item-arrow">
                                ›
                            </span>

                        </a>


                        <!-- MI CUENTA -->

                        <a
                            href="panel.php"
                            class="account-menu-item"
                        >

                            <span class="account-menu-item-icon">
                                👤
                            </span>


                            <span class="account-menu-item-text">

                                <strong>
                                    Mi cuenta
                                </strong>

                                <small>
                                    Ver información personal
                                </small>

                            </span>


                            <span class="account-menu-item-arrow">
                                ›
                            </span>

                        </a>


                        <?php if ($es_admin_menu): ?>
                        <!-- Acceso exclusivo para administradores -->
                        <a href="admin/index.php" class="account-menu-item">
                            <span class="account-menu-item-icon">🛡️</span>
                            <span class="account-menu-item-text">
                                <strong>Panel de administración</strong>
                                <small>Gestionar LunarMC</small>
                            </span>
                            <span class="account-menu-item-arrow">›</span>
                        </a>
                        <?php endif; ?>

                        <!-- ======================================
                             MONEDA
                             ====================================== -->

                        <div class="account-currency-item">


                            <span class="account-menu-item-icon">
                                💰
                            </span>


                            <span class="account-menu-item-text">

                                <strong>
                                    Moneda
                                </strong>

                                <small>
                                    Seleccioná cómo ver los precios
                                </small>

                            </span>


                            <select
                                id="currency-selector"
                                class="currency-selector"
                            >

                                <option value="USD">
                                    USD $
                                </option>

                                <option value="ARS">
                                    ARS $
                                </option>

                                <option value="EUR">
                                    EUR €
                                </option>

                            </select>


                        </div>


                        <!-- ======================================
                             TOTAL DEL CARRITO
                             ====================================== -->

                        <div
                            class="account-cart-summary"
                            id="account-cart-total"
                            data-usd="<?php

                            echo htmlspecialchars(
                                number_format(
                                    $total_carrito_usd,
                                    2,
                                    '.',
                                    ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );

                            ?>"
                        >

                            $<?php

                            echo number_format(
                                $total_carrito_usd,
                                2,
                                '.',
                                ','
                            );

                            ?>

                            USD

                        </div>


                    </div>


                    <!-- ==========================================
                         PIE DEL MENÚ
                         ========================================== -->

                    <div class="account-menu-footer">


                        <a
                            href="logout.php"
                            class="account-menu-logout"
                        >
                            🚪 Cerrar sesión
                        </a>


                    </div>


                </div>

            </div>


        <?php else: ?>


            <!-- ==================================================
                 USUARIO NO LOGUEADO
                 ================================================== -->

            <a
                href="login.php"
                class="btn-login"
            >
                Iniciar sesión
            </a>


            <a
                href="registro.php"
                class="btn-register-link"
            >

                <span class="btn-register">
                    Regístrate 🔑
                </span>

            </a>


        <?php endif; ?>


        <!-- ======================================================
             MODO OSCURO / CLARO
             ====================================================== -->

        <button
            type="button"
            id="toggle-theme-btn"
        >
            ☀️ Modo Claro
        </button>


    </div>

</header>

<!-- Controles comunes: funcionan aunque index-.js no esté cargado en esta página. -->
<script>
(() => {
    'use strict';
    if (window.__lunarmcHeaderControls) return;
    window.__lunarmcHeaderControls = true;
    const IP = 'lunarmc.cc';
    function aplicarTema() {
        let preferencia = null;
        try { preferencia = localStorage.getItem('lunarmc-tema'); } catch (_) {}
        if (preferencia === 'claro') document.body.classList.remove('dark-mode');
        if (preferencia === 'oscuro') document.body.classList.add('dark-mode');
        const boton = document.getElementById('toggle-theme-btn');
        if (boton) boton.textContent = document.body.classList.contains('dark-mode') ? '☀️ Modo Claro' : '🌙 Modo Oscuro';
    }
    function inicializar() {
        aplicarTema();
        const tema = document.getElementById('toggle-theme-btn');
        if (tema && !tema.dataset.lunarmcBound) {
            tema.dataset.lunarmcBound = '1';
            tema.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                try { localStorage.setItem('lunarmc-tema', document.body.classList.contains('dark-mode') ? 'oscuro' : 'claro'); } catch (_) {}
                aplicarTema();
            });
        }
        const copiar = document.getElementById('copy-ip-btn');
        if (copiar && !copiar.dataset.lunarmcBound) {
            copiar.dataset.lunarmcBound = '1';
            copiar.addEventListener('click', async () => {
                let exito = false;
                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(IP);
                        exito = true;
                    }
                } catch (_) {}
                if (!exito) {
                    const campo = document.createElement('textarea');
                    campo.value = IP;
                    campo.style.position = 'fixed';
                    campo.style.opacity = '0';
                    document.body.appendChild(campo);
                    campo.focus(); campo.select();
                    try { exito = document.execCommand('copy'); } catch (_) {}
                    campo.remove();
                }
                copiar.textContent = exito ? '¡IP copiada!' : 'No se pudo copiar';
                setTimeout(() => { copiar.textContent = 'IP: ' + IP; }, 2000);
            });
        }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', inicializar, {once:true});
    else inicializar();
})();
</script>




<!-- ==========================================================
     FECHA Y HORA
     ========================================================== -->

<div
    class="datetime-banner"
    id="datetime-banner"
>

    Fecha:

    <?php echo date("d/m/Y"); ?>

    |

    Hora actual:

    <span id="clock">

        <?php echo date("H:i:s"); ?>

    </span>

</div>





<!-- LunarMC: mismo encuadre que la referencia: cabeza + parte del torso arriba, cuerpo completo abajo. -->
<style>
/* Mantiene el botón original. Sólo cambia el encuadre de la textura dentro de su ventana. */
html body header.header-top .header-right #open-account-menu.account-user .account-skin-wrapper.lunar-avatar-top {
    position: relative !important;
    width: 64px !important;
    height: 60px !important;
    flex: 0 0 64px !important;
    overflow: hidden !important;
}
/* El mismo personaje del desplegable, ampliado 2.2 veces y recortado:
   se ve la cabeza grande y sólo el comienzo del torso, como la captura. */
html body header.header-top .header-right #open-account-menu.account-user .account-skin-wrapper.lunar-avatar-top > canvas.lunar-head-avatar {
    position: absolute !important;
    display: block !important;
    width: 64px !important;
    height: 64px !important;
    min-width: 64px !important;
    max-width: 64px !important;
    min-height: 64px !important;
    max-height: 64px !important;
    top: 0 !important;
    left: 0 !important;
    right: auto !important;
    bottom: auto !important;
    transform-origin: center top !important;
    transform: translateY(-3px) scale(2.2) !important;
    object-fit: fill !important;
    image-rendering: pixelated !important;
    pointer-events: none !important;
}
/* Respaldo: misma imagen de cuerpo entero, recortada como el botón original. */
html body header.header-top .header-right #open-account-menu.account-user .account-skin-wrapper.lunar-avatar-top > img.lunar-avatar-head-fallback {
    position: absolute !important;
    display: block !important;
    width: 60px !important;
    height: auto !important;
    min-width: 60px !important;
    max-width: 60px !important;
    min-height: 0 !important;
    max-height: none !important;
    top: 1px !important;
    left: 50% !important;
    right: auto !important;
    bottom: auto !important;
    transform: translateX(-50%) !important;
    object-fit: contain !important;
    pointer-events: none !important;
}
/* Desplegable: personaje completo y de mayor tamaño, en 64x64. */
html body #account-menu-overlay .account-menu .account-menu-skin-wrapper.lunar-avatar-menu {
    position: relative !important;
    width: 64px !important;
    height: 64px !important;
    flex: 0 0 64px !important;
    overflow: hidden !important;
}
html body #account-menu-overlay .account-menu .account-menu-skin-wrapper.lunar-avatar-menu > canvas.lunar-menu-skin-canvas {
    position: absolute !important;
    display: block !important;
    width: 64px !important;
    height: 64px !important;
    top: 0 !important;
    left: 0 !important;
    transform: none !important;
    pointer-events: none !important;
}
html body #account-menu-overlay .account-menu .account-menu-skin-wrapper.lunar-avatar-menu > img.lunar-avatar-body-fallback {
    position: absolute !important;
    display: block !important;
    width: auto !important;
    height: 72px !important;
    max-width: none !important;
    max-height: none !important;
    top: 0 !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    object-fit: contain !important;
}
/* IMPORTANTE: ocultar la imagen MC-Heads cuando ya se cargó el canvas 3D.
   Los selectores anteriores de img/canvas usan display:block !important;
   y podían sobreescribir el atributo hidden. */
html body header.header-top .header-right #open-account-menu.account-user .account-skin-wrapper.lunar-avatar-top > img.lunar-avatar-head-fallback[hidden],
html body header.header-top .header-right #open-account-menu.account-user .account-skin-wrapper.lunar-avatar-top > canvas.lunar-head-avatar[hidden],
html body #account-menu-overlay .account-menu .account-menu-skin-wrapper.lunar-avatar-menu > img.lunar-avatar-body-fallback[hidden],
html body #account-menu-overlay .account-menu .account-menu-skin-wrapper.lunar-avatar-menu > canvas.lunar-menu-skin-canvas[hidden] {
    display: none !important;
}
@media (max-width: 650px) {
    html body header.header-top .header-right #open-account-menu.account-user .account-skin-wrapper.lunar-avatar-top {
        width: 56px !important;
        height: 54px !important;
        flex-basis: 56px !important;
    }
    html body header.header-top .header-right #open-account-menu.account-user .account-skin-wrapper.lunar-avatar-top > canvas.lunar-head-avatar {
        left: -4px !important;
    }
}
</style>
<script defer src="https://cdn.jsdelivr.net/npm/skinview3d@3.4.2/bundles/skinview3d.bundle.js"></script>
<script>
(() => {
    'use strict';
    async function iniciarAvataresLunar() {
        const topCanvas = document.querySelector('[data-lunarmc-head-avatar]');
        const menuCanvas = document.querySelector('[data-lunarmc-menu-skin]');
        if (!topCanvas && !menuCanvas) return;
        // Si la librería falla, se dejan las imágenes de respaldo visibles.
        if (!window.skinview3d || typeof window.skinview3d.SkinViewer !== 'function') return;
        let url = null;
        const viewers = [];
        try {
            const res = await fetch('/lunarmc/skin_texture.php', { credentials: 'same-origin', cache: 'no-store' });
            if (!res.ok || !(res.headers.get('content-type') || '').includes('image/png')) {
                throw new Error('No se pudo cargar la textura de la cuenta: HTTP ' + res.status);
            }
            url = URL.createObjectURL(await res.blob());
            // Se usa EXACTAMENTE el mismo render 3D para ambos lugares.
            // El CSS superior sólo lo amplía y recorta: cabeza y un poco de torso.
            for (const canvas of [topCanvas, menuCanvas]) {
                if (!canvas) continue;
                let viewer;
                try {
                    viewer = new window.skinview3d.SkinViewer({ canvas, width: 128, height: 128 });
                    viewer.autoRotate = false;
                    viewer.zoom = 0.92;
                    if (viewer.controls) {
                        viewer.controls.enableRotate = false;
                        viewer.controls.enableZoom = false;
                        viewer.controls.enablePan = false;
                    }
                    if (viewer.playerObject) viewer.playerObject.rotation.y = 0.24;
                    await viewer.loadSkin(url);
                    viewers.push(viewer);
                    canvas.hidden = false;
                    const respaldo = canvas.parentElement.querySelector('.lunar-avatar-fallback');
                    if (respaldo) respaldo.hidden = true;
                } catch (err) {
                    if (viewer) viewer.dispose();
                    console.warn('LunarMC: no se pudo dibujar un avatar; se mantiene respaldo.', err);
                }
            }
            if (viewers.length) {
                window.lunarMiniSkinViewers = (window.lunarMiniSkinViewers || []).concat(viewers);
                window.lunarMiniSkinTextureUrls = (window.lunarMiniSkinTextureUrls || []).concat(url);
            } else {
                URL.revokeObjectURL(url);
            }
        } catch (err) {
            if (url) URL.revokeObjectURL(url);
            console.warn('LunarMC: textura no disponible; se mantiene respaldo.', err);
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarAvataresLunar, { once: true });
    } else {
        iniciarAvataresLunar();
    }
})();
</script>

<?php if ($es_inicio): ?>


    <!-- ======================================================
         MENÚ COMPACTO DEL INICIO
         ====================================================== -->

    <nav class="home-nav">


        <a
            href="index.php"
            class="home-nav-link active"
        >
            Inicio
        </a>


        <a
            href="rangos.php"
            class="home-nav-link"
        >
            🏆 Rangos
        </a>


        <a
            href="extras.php"
            class="home-nav-link"
        >
            ✦ Extras
        </a>


        <a
            href="Acerca de Nosotros.php"
            class="home-nav-link"
        >
            Acerca de Nosotros
        </a>


        <a
            href="carrito.php"
            class="home-nav-link"
        >
            🛒 Carrito
        </a>


    </nav>



<?php else: ?>


    <!-- ======================================================
         HEADER NORMAL PARA LAS DEMÁS PÁGINAS
         ====================================================== -->

    <section class="header-container text-center">


        <div class="banner-box">

            <img
                src="imagen del servidor lunar.png"
                alt="LunarMC Banner"
                class="banner-img"
            >

        </div>


        <h1 class="brand-title">

            <?php

            echo defined('APP_NAME')
                ? htmlspecialchars((string) APP_NAME, ENT_QUOTES, 'UTF-8')
                : 'LunarMC Network';

            ?>

        </h1>


        <div class="discord-box-wrapper">


            <div id="discord-community">


                <div class="discord-title">
                    Servidor de discord
                </div>


                <a
                    href="<?php

                    echo defined('DISCORD_URL')
                        ? htmlspecialchars((string) DISCORD_URL, ENT_QUOTES, 'UTF-8')
                        : 'https://discord.gg/BdbZcmjm';

                    ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Haz clic aquí para unirte
                </a>


            </div>

        </div>


        <nav class="main-nav">


            <div class="dropdown">


                <button
                    type="button"
                    class="dropdown-btn"
                >

                    Tridentbox

                    <span class="arrow">
                        ▼
                    </span>

                </button>


                <div class="dropdown-menu">


                    <a
                        href="rangos.php"
                        class="dropdown-item"
                    >
                        Rangos
                    </a>


                    <a
                        href="extras.php"
                        class="dropdown-item"
                    >
                        Extras
                    </a>


                </div>

            </div>


            <a
                href="index.php"
                class="nav-link"
            >
                Inicio
            </a>


            <a
                href="Acerca de Nosotros.php"
                class="nav-link"
            >
                Acerca de Nosotros
            </a>


        </nav>


    </section>


<?php endif; ?>