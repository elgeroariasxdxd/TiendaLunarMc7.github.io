<?php require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$precios_catalogo = [];
try {
    require_once __DIR__ . '/includes/database.php';
    $stmtCatalogo = $pdo->prepare('SELECT codigo, nombre, precio, activo FROM productos');
    $stmtCatalogo->execute();
    foreach ($stmtCatalogo->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $precios_catalogo[$fila['codigo']] = $fila;
    }
} catch (PDOException $e) {
    error_log('LunarMC tienda: ' . $e->getMessage());
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
        Tienda LunarMC - Rangos
    </title>

    <link rel="stylesheet" href="index-.css?v=20261027-fix1">
    <!-- Cargar JS antes de que termine la página; defer espera al HTML. -->
    <script src="index-.js?v=20261027-fix1" defer></script>




</head>


<body class="dark-mode">


<?php include 'includes/header.php'; ?>
<script>
window.LUNARMC_PRECIOS = <?php echo json_encode(array_map(static function ($p) {
    return ['precio' => number_format((float)$p['precio'], 2, '.', ''), 'activo' => (int)$p['activo']];
}, $precios_catalogo), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>



<main class="main-container text-center">


    <section class="lunar-ranks" aria-labelledby="lunar-ranks-title">
      <div class="lunar-ranks-hero">
        <div class="lunar-ranks-hero-text">
           
          <span class="lunar-ranks-eyebrow"><span class="lunar-dot"></span> TIENDA OFICIAL · LUNARMC NETWORK</span>
          <h1 id="lunar-ranks-title">Elevá tu experiencia <em>en LunarMC.</em></h1>
          <p>Explorá nuestros rangos permanentes y elegí el que mejor se adapte a tu aventura.</p>
          <div class="lunar-ranks-pills"><span>✦ Rangos permanentes</span><span>✦ Beneficios exclusivos</span><span>✦ Precios actualizados</span></div>
        </div>
        <!-- Dragón flotante; sin contenedores hero duplicados. -->
        <img src="dragon-lunarmc.png" class="lunar-hero-dragon" alt="Dragón de LunarMC">
        <div class="lunar-hero-decoration" aria-hidden="true"><span>✦</span><span>☾</span><span>✧</span></div>
      </div>
      <div class="lunar-ranks-toolbar"><div><span class="lunar-section-kicker">CATÁLOGO DE RANGOS</span><h2>Encontrá tu rango ideal</h2></div><span class="lunar-ranks-count">6 rangos</span></div>
      <!-- Formulario POST compartido: funciona con y sin JavaScript. -->
      <form id="lunar-ranks-cart-form" method="post" action="agregar carrito.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) ($_SESSION['csrf_token'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
      </form>
      <div class="store-grid lunar-ranks-grid">
        <article class="product-card lunar-rank-card lunar-rank-sun">
          <div class="lunar-rank-topline"><span class="lunar-rank-tier">RANGO INICIAL</span></div>
          <div class="lunar-rank-art"><img src="sun rango.png" alt="Rango Sun" class="product-img" loading="lazy"></div>
          <div class="lunar-rank-body"><h3 class="product-title">Sun</h3><p class="lunar-rank-description">Un rango permanente para disfrutar más de LunarMC.</p>
          <div class="lunar-rank-price-caption">PRECIO DEL RANGO</div>
          <div class="product-pricing lunar-rank-pricing"><span class="product-price"><?php echo (isset($precios_catalogo['sun']) && (int)$precios_catalogo['sun']['activo'] === 1) ? number_format((float)$precios_catalogo['sun']['precio'], 2, '.', '') : 'No disponible'; ?></span><span class="product-currency">USD</span></div>
          <div class="product-actions lunar-rank-actions">
            <button type="button" class="btn-info" title="Más información sobre Sun" aria-label="Ver beneficios de Sun" data-rango="sun">ⓘ <span>Beneficios</span></button>
            <?php if (isset($precios_catalogo['sun']) && (int)$precios_catalogo['sun']['activo'] === 1): ?>
            <button type="submit" form="lunar-ranks-cart-form" name="producto" value="sun" class="btn-add-cart" data-product="sun">Agregar al carrito <span aria-hidden="true">→</span></button>
            <?php else: ?><span class="lunar-rank-unavailable">No disponible</span><?php endif; ?>
          </div></div>
        </article>
        <article class="product-card lunar-rank-card lunar-rank-venus">
          <div class="lunar-rank-topline"><span class="lunar-rank-tier">SIGUIENTE NIVEL</span></div>
          <div class="lunar-rank-art"><img src="rango venus.png" alt="Rango Venus" class="product-img" loading="lazy"></div>
          <div class="lunar-rank-body"><h3 class="product-title">Venus</h3><p class="lunar-rank-description">Un rango permanente para disfrutar más de LunarMC.</p>
          <div class="lunar-rank-price-caption">PRECIO DEL RANGO</div>
          <div class="product-pricing lunar-rank-pricing"><span class="product-price"><?php echo (isset($precios_catalogo['venus']) && (int)$precios_catalogo['venus']['activo'] === 1) ? number_format((float)$precios_catalogo['venus']['precio'], 2, '.', '') : 'No disponible'; ?></span><span class="product-currency">USD</span></div>
          <div class="product-actions lunar-rank-actions">
            <button type="button" class="btn-info" title="Más información sobre Venus" aria-label="Ver beneficios de Venus" data-rango="venus">ⓘ <span>Beneficios</span></button>
            <?php if (isset($precios_catalogo['venus']) && (int)$precios_catalogo['venus']['activo'] === 1): ?>
            <button type="submit" form="lunar-ranks-cart-form" name="producto" value="venus" class="btn-add-cart" data-product="venus">Agregar al carrito <span aria-hidden="true">→</span></button>
            <?php else: ?><span class="lunar-rank-unavailable">No disponible</span><?php endif; ?>
          </div></div>
        </article>
        <article class="product-card lunar-rank-card lunar-rank-venus-plus">
          <div class="lunar-rank-topline"><span class="lunar-rank-tier">RANGO MEJORADO</span></div>
          <div class="lunar-rank-art"><img src="rango venus +.png" alt="Rango Venus +" class="product-img" loading="lazy"></div>
          <div class="lunar-rank-body"><h3 class="product-title">Venus +</h3><p class="lunar-rank-description">Un rango permanente para disfrutar más de LunarMC.</p>
          <div class="lunar-rank-price-caption">PRECIO DEL RANGO</div>
          <div class="product-pricing lunar-rank-pricing"><span class="product-price"><?php echo (isset($precios_catalogo['venus_plus']) && (int)$precios_catalogo['venus_plus']['activo'] === 1) ? number_format((float)$precios_catalogo['venus_plus']['precio'], 2, '.', '') : 'No disponible'; ?></span><span class="product-currency">USD</span></div>
          <div class="product-actions lunar-rank-actions">
            <button type="button" class="btn-info" title="Más información sobre Venus +" aria-label="Ver beneficios de Venus +" data-rango="venus_plus">ⓘ <span>Beneficios</span></button>
            <?php if (isset($precios_catalogo['venus_plus']) && (int)$precios_catalogo['venus_plus']['activo'] === 1): ?>
            <button type="submit" form="lunar-ranks-cart-form" name="producto" value="venus_plus" class="btn-add-cart" data-product="venus_plus">Agregar al carrito <span aria-hidden="true">→</span></button>
            <?php else: ?><span class="lunar-rank-unavailable">No disponible</span><?php endif; ?>
          </div></div>
        </article>
        <article class="product-card lunar-rank-card lunar-rank-nova">
          <div class="lunar-rank-topline"><span class="lunar-rank-tier">RANGO AVANZADO</span></div>
          <div class="lunar-rank-art"><img src="rango nova.png" alt="Rango Nova" class="product-img" loading="lazy"></div>
          <div class="lunar-rank-body"><h3 class="product-title">Nova</h3><p class="lunar-rank-description">Un rango permanente para disfrutar más de LunarMC.</p>
          <div class="lunar-rank-price-caption">PRECIO DEL RANGO</div>
          <div class="product-pricing lunar-rank-pricing"><span class="product-price"><?php echo (isset($precios_catalogo['nova']) && (int)$precios_catalogo['nova']['activo'] === 1) ? number_format((float)$precios_catalogo['nova']['precio'], 2, '.', '') : 'No disponible'; ?></span><span class="product-currency">USD</span></div>
          <div class="product-actions lunar-rank-actions">
            <button type="button" class="btn-info" title="Más información sobre Nova" aria-label="Ver beneficios de Nova" data-rango="nova">ⓘ <span>Beneficios</span></button>
            <?php if (isset($precios_catalogo['nova']) && (int)$precios_catalogo['nova']['activo'] === 1): ?>
            <button type="submit" form="lunar-ranks-cart-form" name="producto" value="nova" class="btn-add-cart" data-product="nova">Agregar al carrito <span aria-hidden="true">→</span></button>
            <?php else: ?><span class="lunar-rank-unavailable">No disponible</span><?php endif; ?>
          </div></div>
        </article>
        <article class="product-card lunar-rank-card lunar-rank-lunar lunar-rank-featured">
          <div class="lunar-rank-topline"><span class="lunar-rank-tier">RANGO DESTACADO</span><span class="lunar-rank-feature-tag">DESTACADO</span></div>
          <div class="lunar-rank-art"><img src="rango lunar.png" alt="Rango Lunar" class="product-img" loading="lazy"></div>
          <div class="lunar-rank-body"><h3 class="product-title">Lunar</h3><p class="lunar-rank-description">Un rango permanente para disfrutar más de LunarMC.</p>
          <div class="lunar-rank-price-caption">PRECIO DEL RANGO</div>
          <div class="product-pricing lunar-rank-pricing"><span class="product-price"><?php echo (isset($precios_catalogo['lunar']) && (int)$precios_catalogo['lunar']['activo'] === 1) ? number_format((float)$precios_catalogo['lunar']['precio'], 2, '.', '') : 'No disponible'; ?></span><span class="product-currency">USD</span></div>
          <div class="product-actions lunar-rank-actions">
            <button type="button" class="btn-info" title="Más información sobre Lunar" aria-label="Ver beneficios de Lunar" data-rango="lunar">ⓘ <span>Beneficios</span></button>
            <?php if (isset($precios_catalogo['lunar']) && (int)$precios_catalogo['lunar']['activo'] === 1): ?>
            <button type="submit" form="lunar-ranks-cart-form" name="producto" value="lunar" class="btn-add-cart" data-product="lunar">Agregar al carrito <span aria-hidden="true">→</span></button>
            <?php else: ?><span class="lunar-rank-unavailable">No disponible</span><?php endif; ?>
          </div></div>
        </article>
        <article class="product-card lunar-rank-card lunar-rank-summer">
          <div class="lunar-rank-topline"><span class="lunar-rank-tier">EDICIÓN ESPECIAL</span></div>
          <div class="lunar-rank-art"><img src="rango summer.png" alt="Rango Summer" class="product-img" loading="lazy"></div>
          <div class="lunar-rank-body"><h3 class="product-title">Summer</h3><p class="lunar-rank-description">Un rango permanente para disfrutar más de LunarMC.</p>
          <div class="lunar-rank-price-caption">PRECIO DEL RANGO</div>
          <div class="product-pricing lunar-rank-pricing"><span class="product-price"><?php echo (isset($precios_catalogo['summer']) && (int)$precios_catalogo['summer']['activo'] === 1) ? number_format((float)$precios_catalogo['summer']['precio'], 2, '.', '') : 'No disponible'; ?></span><span class="product-currency">USD</span></div>
          <div class="product-actions lunar-rank-actions">
            <button type="button" class="btn-info" title="Más información sobre Summer" aria-label="Ver beneficios de Summer" data-rango="summer">ⓘ <span>Beneficios</span></button>
            <?php if (isset($precios_catalogo['summer']) && (int)$precios_catalogo['summer']['activo'] === 1): ?>
            <button type="submit" form="lunar-ranks-cart-form" name="producto" value="summer" class="btn-add-cart" data-product="summer">Agregar al carrito <span aria-hidden="true">→</span></button>
            <?php else: ?><span class="lunar-rank-unavailable">No disponible</span><?php endif; ?>
          </div></div>
        </article>
      </div>
      <p class="lunar-ranks-note">Los precios se consultan desde el catálogo de LunarMC. Consultá los beneficios de cada rango antes de agregarlo al carrito.</p>
    </section>

    <!-- ==================================================
         MODAL DE INFORMACIÓN
         ================================================== -->

    <div
        id="info-modal"
        class="modal-overlay"
        aria-hidden="true"
    >

        <div
            class="modal-content"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title"
        >

            <button
                type="button"
                class="modal-close"
                id="btn-close-modal"
                aria-label="Cerrar"
            >
                &times;
            </button>


            <div class="modal-body">


                <div class="modal-left">

                    <img
                        id="modal-img"
                        src=""
                        alt="Rango"
                        class="modal-rank-img"
                    >

                    <h2
                        id="modal-title"
                        class="modal-rank-title"
                    ></h2>


                    <div class="modal-pricing">

                        <span
                            id="modal-price-old"
                            class="product-price-old"
                        ></span>

                        <span
                            id="modal-price"
                            class="product-price"
                        ></span>

                        <span class="product-currency">
                            USD
                        </span>

                    </div>


                    <button
                        type="button"
                        class="btn-add-cart-large"
                        style="width: 100%; border: none; cursor: pointer;"
                    >
                        AGREGAR
                    </button>

                </div>


                <div
                    class="modal-right"
                    id="modal-details-content"
                ></div>


            </div>

        </div>

    </div>


</main>


<?php

if (file_exists(__DIR__ . '/includes/footer.php')) {

    include __DIR__ . '/includes/footer.php';

}

?>



