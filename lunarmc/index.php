<?php

require_once __DIR__ . '/config.php';

// Comprobar si el usuario tiene permisos de administrador.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$esAdministrador = false;

if (!empty($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/includes/database.php';

    try {
        $stmtAdmin = $pdo->prepare("SELECT rol_id FROM usuarios WHERE id = :id LIMIT 1");
        $stmtAdmin->execute([':id' => (int)$_SESSION['usuario_id']]);
        $esAdministrador = (int)$stmtAdmin->fetchColumn() === 2;
    } catch (PDOException $e) {
        error_log('LunarMC index admin: ' . $e->getMessage());
    }
}

// Mostrar el acceso al panel únicamente a administradores autenticados.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$esAdministradorLunar = false;
if (isset($_SESSION['usuario_id'])) {
    try {
        require_once __DIR__ . '/includes/database.php';
        $consultaRolLunar = $pdo->prepare('SELECT rol_id FROM usuarios WHERE id = :id LIMIT 1');
        $consultaRolLunar->execute([':id' => $_SESSION['usuario_id']]);
        $esAdministradorLunar = (int) $consultaRolLunar->fetchColumn() === 2;
    } catch (Throwable $e) {
        error_log('LunarMC: no se pudo verificar el rol: ' . $e->getMessage());
    }
}


date_default_timezone_set(
    'America/Argentina/Buenos_Aires'
);


/*
|--------------------------------------------------------------------------
| META MENSUAL AUTOMÁTICA POR COMPRAS CONFIRMADAS
|--------------------------------------------------------------------------
| Cada pedido pagado o entregado cuenta como UNA compra.
| Los pedidos pendientes y las simulaciones no suman.
| Cambiá 20 por la cantidad de compras que equivalga al 100%.
*/

$objetivo_compras_mensual = 20;
$meta_mensual = 0;
$compras_confirmadas_mes = 0;

try {
    // La columna pedidos.fecha existe en el esquema de LunarMC.
    $inicio_mes = date('Y-m-01 00:00:00');
    $inicio_mes_siguiente = date('Y-m-01 00:00:00', strtotime('first day of next month'));

    require_once __DIR__ . '/includes/database.php';
    $stmt_compras = $pdo->prepare("
        SELECT COUNT(*)
        FROM pedidos
        WHERE estado IN ('pagado', 'entregado')
          AND fecha >= :inicio_mes
          AND fecha < :inicio_mes_siguiente
    " );
    $stmt_compras->execute([
        ':inicio_mes' => $inicio_mes,
        ':inicio_mes_siguiente' => $inicio_mes_siguiente,
    ]);

    $compras_confirmadas_mes = (int) $stmt_compras->fetchColumn();
    $meta_mensual = min(100, max(0,
        ($compras_confirmadas_mes / $objetivo_compras_mensual) * 100
    ));
} catch (Throwable $e) {
    error_log('LunarMC: error al calcular meta mensual: ' . $e->getMessage());
}

?>


<!DOCTYPE html>

<html lang="es">


<head>


    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>

        Tienda Minecraft -

        <?php

        echo defined('APP_NAME')
            ? htmlspecialchars(
                APP_NAME,
                ENT_QUOTES,
                'UTF-8'
            )
            : 'LunarMC';

        ?>

    </title>


    <link rel="stylesheet" href="index-.css?v=20261028-tema3">
    <!-- Cargar JS en HEAD con defer: no depende del footer. -->
    <script src="index-.js?v=20261028-tema3" defer></script>


</head>



<body class="dark-mode home-page">



<?php

include __DIR__ .
    '/includes/header.php';

?>



<main class="home-main lunar-home-v2">
  <section class="lh-hero" aria-labelledby="lh-title">
    <div class="lh-hero-shine" aria-hidden="true"></div>
    <div class="lh-hero-inner">
      <span class="lh-eyebrow">✦ TIENDA OFICIAL · LUNARMC NETWORK</span>
      <h1 id="lh-title">LUNAR<span>MC</span></h1>
      <p>Tu aventura comienza acá. Descubrí rangos, objetos especiales y mejoras para disfrutar LunarMC a tu manera.</p>
      <div class="lh-hero-actions">
        <a class="lh-btn lh-btn-primary" href="rangos.php">EXPLORAR RANGOS <span aria-hidden="true">↗</span></a>
        <a class="lh-btn lh-btn-outline" href="extras.php">VER EXTRAS <span aria-hidden="true">✦</span></a>
      </div>
      <div class="lh-hero-points"><span>✦ Rangos permanentes</span><span>✦ Extras exclusivos</span><span>✦ Pedidos en tu cuenta</span></div>
    </div>
    <img class="lh-hero-art" src="dragon-lunarmc.png" alt="Dragón de LunarMC" onerror="this.style.display='none'">
  </section>

  <section class="lh-shop-banner" aria-labelledby="lh-shop-title">
    <div class="lh-shop-copy">
      <span class="lh-eyebrow">✦ EXPLORÁ LA TIENDA</span>
      <h2 id="lh-shop-title">ELEGÍ TU PRÓXIMA AVENTURA</h2>
      <p>Mejorá tu experiencia en el servidor con rangos y objetos especiales.</p>
      <div class="lh-shop-actions">
        <a class="lh-btn lh-btn-gold" href="rangos.php">COMPRAR RANGOS <span aria-hidden="true">→</span></a>
        <a class="lh-btn lh-btn-glass" href="extras.php">EXPLORAR EXTRAS</a>
      </div>
    </div>
    <img src="rango lunar.png" alt="Rango Lunar" class="lh-shop-art" loading="lazy">
  </section>

  <section class="lh-stats" aria-label="Información de la comunidad">
    <article class="lh-panel lh-goal">
      <div class="lh-panel-head"><span class="lh-panel-icon">🏆</span><div><span class="lh-panel-kicker">COMUNIDAD</span><h2>META MENSUAL</h2></div></div>
      <div class="lh-goal-number"><span>Progreso de este mes</span><strong><?php echo number_format($meta_mensual, 0); ?>%</strong></div>
      <div class="lh-progress" role="progressbar" aria-label="Meta mensual" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo (int) $meta_mensual; ?>"><span style="width: <?php echo $meta_mensual; ?>%"></span></div>
    </article>
    <article class="lh-panel lh-recent">
      <div class="lh-panel-head"><span class="lh-panel-icon">✦</span><div><span class="lh-panel-kicker">ACTIVIDAD</span><h2>COMPRAS RECIENTES</h2></div></div>
      <div class="lh-recent-content">
      <?php if (file_exists(__DIR__ . '/includes/pagos_recientes.php')) { include __DIR__ . '/includes/pagos_recientes.php'; } else { echo '<p>Las compras recientes aparecerán aquí.</p>'; } ?>
      </div>
    </article>
  </section>

  <section class="lh-categories" aria-labelledby="lh-categories-title">
    <div class="lh-section-head"><div><span class="lh-eyebrow">NUESTRA TIENDA</span><h2 id="lh-categories-title">DESCUBRÍ LUNARMC</h2></div></div>
    <div class="lh-category-grid">
      <a href="rangos.php" class="lh-category lh-category-ranks"><div><span class="lh-category-tag">MEJORAS PERMANENTES</span><h3>RANGOS</h3><p>Beneficios exclusivos para tu aventura.</p><span class="lh-category-link">VER RANGOS →</span></div><img src="rango lunar.png" alt="" loading="lazy"></a>
      <a href="extras.php" class="lh-category lh-category-extras"><div><span class="lh-category-tag">PERSONALIZACIÓN</span><h3>EXTRAS</h3><p>Objetos, cosméticos y mucho más.</p><span class="lh-category-link">VER EXTRAS →</span></div><img src="imagen de tags.png" alt="" loading="lazy"></a>
    </div>
  </section>

  <section class="lh-info-grid" aria-label="Información útil">
    <article class="lh-info"><span class="lh-info-icon">⚡</span><h3>GESTIONÁ TUS PEDIDOS</h3><p>Consultá el estado de tus compras desde tu cuenta de LunarMC.</p></article>
    <article class="lh-info"><span class="lh-info-icon">🛡️</span><h3>COMPRAS REGISTRADAS</h3><p>Los pedidos quedan asociados a tu usuario para que puedas consultarlos.</p></article>
    <article class="lh-info"><span class="lh-info-icon">💜</span><h3>COMUNIDAD</h3><p>Enterate de las novedades y participá en nuestro Discord.</p><a href="<?php echo htmlspecialchars(defined('DISCORD_URL') ? DISCORD_URL : 'https://discord.gg/BdbZcmjm', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">UNIRME AL DISCORD →</a></article>
  </section>

  <section class="lh-disclaimer"><p>Usá el mismo nick con el que jugás en <strong>lunarmc.cc</strong>. Las compras se registran en tu cuenta; verificá las condiciones antes de confirmar un pedido.</p><p>LunarMC no está afiliado ni respaldado por Mojang Studios o Microsoft. <a href="Acerca de Nosotros.php">Más información</a>.</p></section>

  <div class="lh-existing-content">
    <?php if (file_exists(__DIR__ . '/includes/gallery.php')) { include __DIR__ . '/includes/gallery.php'; } ?>
    <?php if (file_exists(__DIR__ . '/includes/faq.php')) { include __DIR__ . '/includes/faq.php'; } ?>
  </div>
</main>


<?php

if (
    file_exists(
        __DIR__ .
        '/includes/footer.php'
    )
) {

    include __DIR__ .
        '/includes/footer.php';

}

?>
