<?php

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$catalogo_error = '';
if (!empty($_SESSION['carrito'])) {
    try {
        require_once __DIR__ . '/includes/database.php';
        $stmt = $pdo->prepare('SELECT nombre, precio FROM productos WHERE codigo = :codigo AND activo = 1 LIMIT 1');
        foreach ($_SESSION['carrito'] as $codigo => &$item) {
            $stmt->execute([':codigo' => (string)$codigo]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fila) {
                $catalogo_error = 'Hay productos no disponibles. Quitalos del carrito para continuar.';
                continue;
            }
            $item['nombre'] = $fila['nombre'];
            $item['precio'] = (float)$fila['precio'];
        }
        unset($item);
    } catch (PDOException $e) {
        error_log('LunarMC catálogo carrito: ' . $e->getMessage());
        $catalogo_error = 'No se pudo verificar el catálogo. Intentá nuevamente.';
    }
}

/* ==========================================================
   ACTUALIZAR CANTIDAD
   ========================================================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['producto'], $_POST['cambio']) &&
    isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token'])
) {

    $producto_id = (string) $_POST['producto'];
    $cambio = (int) $_POST['cambio'];

    if (
        isset($_SESSION['carrito'][$producto_id]) &&
        ($cambio === 1 || $cambio === -1)
    ) {

        $cantidad_actual = (int) (
            $_SESSION['carrito'][$producto_id]['cantidad'] ?? 1
        );

        $nueva_cantidad = $cantidad_actual + $cambio;

        if ($nueva_cantidad <= 0) {

            unset(
                $_SESSION['carrito'][$producto_id]
            );

        } else {

            $_SESSION['carrito'][$producto_id]['cantidad'] =
                $nueva_cantidad;

        }
    }

    header('Location: carrito.php');
    exit;
}


/* ==========================================================
   ELIMINAR PRODUCTO
   ========================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'], $_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token'])) {

    $producto_id = (string) $_POST['eliminar'];

    if (
        isset($_SESSION['carrito']) &&
        isset($_SESSION['carrito'][$producto_id])
    ) {

        unset(
            $_SESSION['carrito'][$producto_id]
        );

    }

    header('Location: carrito.php');
    exit;
}


/* ==========================================================
   USUARIO
   ========================================================== */

$usuario_logueado =
    isset($_SESSION['usuario_id']);

$nick_usuario =
    $usuario_logueado
        ? ($_SESSION['nick'] ?? 'Usuario')
        : 'Invitado';


/* ==========================================================
   CARRITO
   ========================================================== */

$carrito =
    $_SESSION['carrito'] ?? [];

$total = 0;
$cantidad_total = 0;

foreach ($carrito as $producto) {

    $precio =
        (float) (
            $producto['precio'] ?? 0
        );

    $cantidad =
        (int) (
            $producto['cantidad'] ?? 1
        );

    $subtotal =
        $precio * $cantidad;

    $total +=
        $subtotal;

    $cantidad_total +=
        $cantidad;
}


/* ==========================================================
   CABEZA DEL JUGADOR
   ========================================================== */

$skin_usuario =
    'https://mc-heads.net/avatar/' .
    rawurlencode($nick_usuario) .
    '/100';

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tu carrito - LunarMC</title>

   <link rel="stylesheet" href="index-.css?v=20261025">

</head>


<body class="dark-mode">


<?php include __DIR__ . '/includes/header.php'; ?>


<main class="lunarmc-cart-page">
<?php if ($catalogo_error !== ''): ?><p role="alert" style="color:#ff7777;text-align:center"><?php echo htmlspecialchars($catalogo_error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>


    <!-- ======================================================
         TÍTULO
         ====================================================== -->

    <div class="lunarmc-cart-heading">

        <h1>
            Tu carrito
        </h1>

        <p>
            Revisá tus productos antes de finalizar la compra.
        </p>

    </div>


    <?php if (empty($carrito)): ?>


        <!-- ==================================================
             CARRITO VACÍO
             ================================================== -->

        <div class="lunarmc-cart-box lunarmc-empty">

            <div class="lunarmc-empty-icon">
                🛒
            </div>

            <h2>
                Tu carrito está vacío
            </h2>

            <p>
                Todavía no agregaste ningún producto.
            </p>

            <a href="rangos.php">
                Ver productos
            </a>

        </div>


    <?php else: ?>


        <!-- ==================================================
             CARRITO CON PRODUCTOS
             ================================================== -->

        <div class="lunarmc-cart-layout">


            <!-- ==============================================
                 PRODUCTOS
                 ============================================== -->

            <section class="lunarmc-cart-box">


                <div class="lunarmc-cart-box-title">

                    <h2>
                        Productos
                    </h2>

                    <span class="lunarmc-cart-count">

                        <?php echo (int) $cantidad_total; ?>

                        <?php
                        echo $cantidad_total === 1
                            ? 'producto'
                            : 'productos';
                        ?>

                    </span>

                </div>


                <div class="lunarmc-products">


                    <?php foreach ($carrito as $id => $producto): ?>


                        <?php

                        $precio =
                            (float) (
                                $producto['precio']
                                ?? 0
                            );

                        $cantidad =
                            (int) (
                                $producto['cantidad']
                                ?? 1
                            );

                        $subtotal =
                            $precio * $cantidad;

                        $nombre =
                            $producto['nombre']
                            ?? 'Producto';

                        $imagen =
                            $producto['imagen']
                            ?? '';

                        ?>


                        <article class="lunarmc-cart-item">


                            <!-- IMAGEN -->

                            <div class="lunarmc-product-image">

                                <?php if ($imagen !== ''): ?>

                                    <img
                                        src="<?php
                                        echo htmlspecialchars(
                                            $imagen,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>"
                                        alt="<?php
                                        echo htmlspecialchars(
                                            $nombre,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>"
                                    >

                                <?php else: ?>

                                    <span>📦</span>

                                <?php endif; ?>

                            </div>


                            <!-- INFORMACIÓN -->

                            <div class="lunarmc-product-info">

                                <h3>

                                    <?php
                                    echo htmlspecialchars(
                                        $nombre,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </h3>


                                <span class="lunarmc-product-category">
                                    Producto LunarMC Network
                                </span>


                                <div class="lunarmc-product-unit-price">

                                    Precio unitario:

                                    <strong>

                                        $<?php
                                        echo number_format(
                                            $precio,
                                            2
                                        );
                                        ?>

                                        USD

                                    </strong>

                                </div>

                            </div>


                            <!-- CANTIDAD / TOTAL -->

                            <div class="lunarmc-product-actions">


                                <div class="lunarmc-product-total">

                                    $<?php
                                    echo number_format(
                                        $subtotal,
                                        2
                                    );
                                    ?>

                                    USD

                                </div>


                                <div class="lunarmc-quantity-box">


                                    <form method="POST" action="carrito.php" class="lunarmc-quantity-form" style="display:inline-flex;margin:0">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="producto" value="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="cambio" value="-1">
        <button type="submit" class="lunarmc-quantity-btn" aria-label="Disminuir cantidad">−</button>
    </form>


                                    <span class="lunarmc-quantity-number">

                                        <?php echo $cantidad; ?>

                                    </span>


                                    <form method="POST" action="carrito.php" class="lunarmc-quantity-form" style="display:inline-flex;margin:0">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="producto" value="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="cambio" value="1">
        <button type="submit" class="lunarmc-quantity-btn" aria-label="Aumentar cantidad">+</button>
    </form>


                                </div>


                                <form method="POST" action="carrito.php">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="eliminar" value="<?php echo htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8'); ?>">
<button type="submit" class="lunarmc-remove">Eliminar</button>
</form>


                            </div>


                        </article>


                    <?php endforeach; ?>


                </div>


            </section>


            <!-- ==============================================
                 RESUMEN
                 ============================================== -->

            <aside class="lunarmc-cart-box">


                <!-- ==========================================
                     JUGADOR
                     ========================================== -->

                <div class="lunarmc-player-card">


                    <div class="lunarmc-player-info">

                        <span class="lunarmc-player-label">
                            Comprando para
                        </span>


                        <strong class="lunarmc-player-name">

                            <?php
                            echo htmlspecialchars(
                                $nick_usuario,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </strong>


                        <span class="lunarmc-player-description">

                            Los productos serán entregados
                            a esta cuenta.

                        </span>

                    </div>


                    <img
                        src="<?php
                        echo htmlspecialchars(
                            $skin_usuario,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        alt="Cabeza de <?php
                        echo htmlspecialchars(
                            $nick_usuario,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>"
                        class="lunarmc-player-skin"
                    >


                </div>


                <!-- ==========================================
                     RESUMEN DE PRECIOS
                     ========================================== -->

                <div class="lunarmc-summary">


                    <div class="lunarmc-summary-line">

                        <span>
                            Productos
                        </span>

                        <strong>
                            <?php echo (int) $cantidad_total; ?>
                        </strong>

                    </div>


                    <div class="lunarmc-summary-line">

                        <span>
                            Subtotal
                        </span>

                        <strong>

                            $<?php
                            echo number_format(
                                $total,
                                2
                            );
                            ?>

                            USD

                        </strong>

                    </div>


                    <div class="lunarmc-summary-line">

                        <span>
                            Descuento
                        </span>

                        <strong id="cart-discount">
                            $0.00 USD
                        </strong>

                    </div>


                    <div class="lunarmc-summary-divider"></div>


                    <div class="lunarmc-summary-total">

                        <span>
                            Total
                        </span>

                        <strong id="cart-final-total">

                            $<?php
                            echo number_format(
                                $total,
                                2
                            );
                            ?>

                            USD

                        </strong>

                    </div>


                    <!-- ======================================
                         CUPÓN
                         ====================================== -->

                    <div class="lunarmc-coupon">


                        <label for="coupon-code">
                            ¿Tenés un código de descuento?
                        </label>


                        <div class="lunarmc-coupon-row">

                            <input
                                type="text"
                                id="coupon-code"
                                placeholder="Código"
                                autocomplete="off"
                            >

                            <button
                                type="button"
                                id="apply-coupon"
                            >
                                Aplicar
                            </button>

                        </div>


                        <small
                            id="coupon-message"
                            class="lunarmc-coupon-message"
                        ></small>


                    </div>


                    <!-- ======================================
                         CONFIRMACIONES
                         ====================================== -->

                    <div class="lunarmc-checkout-options">


                        <label class="lunarmc-check">

                            <input
                                type="checkbox"
                                id="confirm-account"
                                name="confirm_account" value="1" form="lunarmc-checkout-form" required
                            >

                            <span>

                                Confirmo que estoy comprando para

                                <strong class="lunarmc-confirm-user">

                                    <?php
                                    echo htmlspecialchars(
                                        $nick_usuario,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </strong>.

                            </span>

                        </label>


                        <label class="lunarmc-check">

                            <input
                                type="checkbox"
                                id="confirm-terms"
                                name="confirm_terms" value="1" form="lunarmc-checkout-form" required
                            >

                            <span>

                                Confirmo que revisé mi pedido
                                y acepto los términos de compra.

                            </span>

                        </label>


                    </div>


                    <!-- ======================================
                         CHECKOUT
                         ====================================== -->

                    <form method="POST" action="procesar pedido.php" id="lunarmc-checkout-form">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
<button
    type="submit"
    id="checkout-button"
    class="lunarmc-checkout-button"
    <?php echo $catalogo_error !== '' ? 'disabled' : ''; ?>
>
    CONTINUAR AL PAGO
</button>
</form>


                    <a
                        href="rangos.php"
                        class="lunarmc-continue"
                    >
                        ← Continuar comprando
                    </a>


                    <br>


                    <a
                        href="vaciar carrito.php"
                        class="lunarmc-clear-cart"
                        onclick="
                            return confirm(
                                '¿Seguro que querés vaciar todo el carrito?'
                            );
                        "
                    >
                        Vaciar carrito
                    </a>


                </div>


            </aside>


        </div>


    <?php endif; ?>


</main>


<?php

if (
    file_exists(
        __DIR__ .
        '/includes/footer.php'
    )
) {

    include
        __DIR__ .
        '/includes/footer.php';

}

?>



<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('lunarmc-checkout-form');
    const account = document.getElementById('confirm-account');
    const terms = document.getElementById('confirm-terms');
    const button = document.getElementById('checkout-button');

    if (!form || !account || !terms || !button) return;

    function actualizarBoton() {
        const confirmado = account.checked && terms.checked;

        button.classList.toggle('disabled', !confirmado);
        button.setAttribute('aria-disabled', String(!confirmado));
    }

    account.addEventListener('change', actualizarBoton);
    terms.addEventListener('change', actualizarBoton);

    form.addEventListener('submit', (event) => {
        if (!account.checked || !terms.checked) {
            event.preventDefault();
            alert('Debés marcar las dos confirmaciones.');
            return;
        }
    });

    actualizarBoton();
});
</script>

<script defer src="index-.js?v=20261028-fix2"></script>
</body>

</html>