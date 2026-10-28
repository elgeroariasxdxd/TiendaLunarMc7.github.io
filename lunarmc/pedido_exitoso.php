
<?php

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| COMPROBAR SESIÓN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| OBTENER ID DEL PEDIDO
|--------------------------------------------------------------------------
*/

$pedido_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($pedido_id <= 0) {
    header('Location: carrito.php');
    exit;
}


/* Obtener solo pedidos pertenecientes al usuario autenticado mediante PDO. */
$usuario_id = (int)$_SESSION['usuario_id'];
try {
    require_once __DIR__ . '/includes/database.php';
    $stmt = $pdo->prepare(
        'SELECT id, total, estado, fecha FROM pedidos WHERE id = :id AND usuario_id = :usuario_id LIMIT 1'
    );
    $stmt->execute([':id' => $pedido_id, ':usuario_id' => $usuario_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pedido) {
        header('Location: carrito.php');
        exit;
    }
    $stmtProductos = $pdo->prepare(
        'SELECT producto, precio, cantidad, subtotal FROM pedido_productos WHERE pedido_id = :pedido_id'
    );
    $stmtProductos->execute([':pedido_id' => $pedido_id]);
    $productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('LunarMC pedido: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo consultar el pedido.');
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

    <title>Pedido realizado - LunarMC</title>

    <link
        rel="stylesheet"
        href="index-.css"
    >

</head>


<body class="dark-mode">


<?php include __DIR__ . '/includes/header.php'; ?>


<main class="main-container text-center">

    <section class="cart-section">


        <div class="cart-empty">


            <h2 class="gold-heading">
                ✅ ¡Pedido creado correctamente!
            </h2>


            <p>
                Tu pedido ha sido registrado en nuestro sistema.
            </p>


            <p>
                <strong>
                    Número de pedido:
                </strong>

                #<?php echo (int)$pedido['id']; ?>
            </p>


            <p>
                <strong>
                    Estado:
                </strong>

                <?php echo htmlspecialchars(
                    ucfirst($pedido['estado']),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </p>


            <p>
                <strong>
                    Fecha:
                </strong>

                <?php echo htmlspecialchars(
                    $pedido['fecha'],
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </p>


        </div>


        <div class="cart-container">


            <?php foreach ($productos as $producto): ?>


                <div class="cart-item">


                    <div class="cart-item-info">

                        <h3>
                            <?php echo htmlspecialchars(
                                $producto['producto'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </h3>


                        <p>
                            Precio:
                            $<?php echo number_format(
                                $producto['precio'],
                                2
                            ); ?>
                            USD
                        </p>


                        <p>
                            Cantidad:
                            <?php echo (int)$producto['cantidad']; ?>
                        </p>

                    </div>


                    <div class="cart-item-total">

                        <strong>
                            $<?php echo number_format(
                                $producto['subtotal'],
                                2
                            ); ?>
                            USD
                        </strong>

                    </div>


                </div>


            <?php endforeach; ?>


            <div class="cart-summary">


                <h3>
                    Total:
                    $<?php echo number_format(
                        $pedido['total'],
                        2
                    ); ?>
                    USD
                </h3>


                <div class="cart-actions">


                    <a
                        href="rangos.php"
                        class="btn-add-cart"
                    >
                        ← Volver a rangos
                    </a>


                    <a
                        href="panel.php"
                        class="btn-cart"
                    >
                        👤 Mi cuenta
                    </a>


                </div>


            </div>


        </div>


    </section>

</main>


<?php

if (file_exists(__DIR__ . '/includes/footer.php')) {
    include __DIR__ . '/includes/footer.php';
}

?>

</body>

</html>
