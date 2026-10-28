
<?php
require_once __DIR__ . '/../verificar_admin.php';

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

try {
    $sql = "
        SELECT
            p.id,
            p.usuario_id,
            p.total,
            p.estado,
            p.fecha,
            u.nick,
            u.email,
            (
                SELECT COUNT(*)
                FROM pedido_productos pp
                WHERE pp.pedido_id = p.id
            ) AS productos
        FROM pedidos p
        LEFT JOIN usuarios u ON u.id = p.usuario_id
        ORDER BY p.fecha DESC, p.id DESC
    ";

    $stmt = $pdo->query($sql);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPedidos = count($pedidos);
    $pendientes = 0;
    $importeRegistrado = 0;

    foreach ($pedidos as $pedido) {
        if ($pedido['estado'] === 'pendiente') {
            $pendientes++;
        }

        $importeRegistrado += (float)$pedido['total'];
    }

} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('No se pudieron cargar los pedidos.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de pedidos - LunarMC</title>

    <link rel="stylesheet" href="../../index-.css?v=12">
    <script src="../../index-.js?v=12" defer></script>
</head>

<body class="admin-dashboard admin-products admin-orders">

<main class="products-container">

    <header class="products-header">
        <div class="products-heading">
            <img
                src="../../imagen del servidor lunar.png"
                alt="LunarMC"
                class="products-logo"
            >

            <div>
                <span class="products-eyebrow">
                    LUNARMC / ADMINISTRACIÓN
                </span>

                <h1>Gestión de <span>pedidos</span></h1>

                <p>Consultá las compras realizadas en la tienda.</p>
            </div>
        </div>

      <div class="admin-header-actions">

    <?php require __DIR__ . '/../tarjeta_admin.php'; ?>

    <button
        type="button"
        id="admin-theme-toggle"
        class="admin-theme-toggle"
    >
        ☀️ Modo claro
    </button>

</div>
    </header>

    <section class="orders-stats">

        <article class="orders-stat">
            <span>Pedidos registrados</span>
            <strong><?php echo $totalPedidos; ?></strong>
        </article>

        <article class="orders-stat">
            <span>Pedidos pendientes</span>
            <strong><?php echo $pendientes; ?></strong>
        </article>

        <article class="orders-stat">
            <span>Importe registrado</span>
            <strong>
                $<?php echo number_format(
                    $importeRegistrado, 2, ',', '.'
                ); ?>
            </strong>
        </article>

    </section>

    <section class="products-panel">

        <div class="orders-heading">
            <div>
                <h2>Historial de pedidos</h2>
                <p>Compras y solicitudes registradas en MySQL.</p>
            </div>
        </div>

        <?php if (!$pedidos): ?>

            <div class="orders-empty">
                <h3>Todavía no hay pedidos</h3>
                <p>
                    Cuando se registren compras de prueba,
                    aparecerán automáticamente acá.
                </p>
            </div>

        <?php else: ?>

            <div class="orders-table-scroll">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Jugador</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <?php
                                $estado = strtolower(
                                    (string)$pedido['estado']
                                );

                                $claseEstado = match ($estado) {
                                    'pendiente' => 'pending',
                                    'pagado', 'aprobado' => 'paid',
                                    'cancelado', 'rechazado' => 'cancelled',
                                    default => 'other'
                                };
                            ?>

                            <tr>
                                <td>
                                    #<?php echo (int)$pedido['id']; ?>
                                </td>

                                <td>
                                    <strong>
                                        <?php echo escapar(
                                            $pedido['nick'] ?? 'Usuario no disponible'
                                        ); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo (int)$pedido['productos']; ?>
                                </td>

                                <td>
                                    $<?php echo number_format(
                                        (float)$pedido['total'],
                                        2,
                                        ',',
                                        '.'
                                    ); ?>
                                </td>

                                <td>
                                    <span class="orders-status orders-status-<?php
                                        echo $claseEstado;
                                    ?>">
                                        <?php echo escapar(
                                            ucfirst($estado)
                                        ); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo escapar($pedido['fecha']); ?>
                                </td>

                                <td>
                                    <a
                                        class="products-btn products-btn-edit"
                                        href="ver.php?id=<?php
                                            echo (int)$pedido['id'];
                                        ?>"
                                    >
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>

    <a href="../index.php" class="products-back">
         🏠 Volver al panel
        
    </a>

</main>

</body>
</html>
