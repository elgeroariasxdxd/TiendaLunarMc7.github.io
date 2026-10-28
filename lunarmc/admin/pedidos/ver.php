
<?php
require_once __DIR__ . '/../verificar_admin.php';

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de pedido inválido.');
}

// Token para impedir cambios de estado desde otras páginas.
if (empty($_SESSION['csrf_pedidos'])) {
    $_SESSION['csrf_pedidos'] = bin2hex(random_bytes(32));
}

$estadosPermitidos = ['pendiente', 'pagado', 'entregado', 'cancelado'];
$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string)($_POST['csrf_token'] ?? '');
    $nuevoEstado = (string)($_POST['estado'] ?? '');
    $confirmacion = (string)($_POST['confirmacion'] ?? '');

    if (!hash_equals($_SESSION['csrf_pedidos'], $token)) {
        $mensajeError = 'La sesión de seguridad expiró. Recargá la página.';
    } elseif (!in_array($nuevoEstado, $estadosPermitidos, true)) {
        $mensajeError = 'El estado seleccionado no es válido.';
    } elseif ($nuevoEstado === 'pagado' && $confirmacion !== '1') {
        $mensajeError = 'Confirmá que verificaste el pago antes de marcarlo como pagado.';
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('SELECT estado FROM pedidos WHERE id = :id FOR UPDATE');
            $stmt->execute([':id' => $id]);
            $actual = $stmt->fetchColumn();
            if ($actual === false) {
                throw new RuntimeException('Pedido no encontrado.');
            }
            $transiciones = [
                'pendiente' => ['pagado', 'cancelado'],
                'pagado' => ['entregado', 'cancelado'],
                'entregado' => [],
                'cancelado' => [],
            ];
            if (!in_array($nuevoEstado, $transiciones[(string)$actual] ?? [], true)) {
                throw new RuntimeException('No se permite cambiar de ' . $actual . ' a ' . $nuevoEstado . '.');
            }
            $stmt = $pdo->prepare('UPDATE pedidos SET estado = :estado WHERE id = :id');
            $stmt->execute([':estado' => $nuevoEstado, ':id' => $id]);
            $pdo->commit();
            header('Location: ver.php?id=' . $id . '&actualizado=1', true, 303);
            exit;
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($error->getMessage());
            $mensajeError = $error instanceof RuntimeException
                ? $error->getMessage()
                : 'No se pudo guardar el estado. Revisá que la columna estado permita ese valor.';
        }
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT
            p.id,
            p.usuario_id,
            p.total,
            p.estado,
            p.fecha,
            u.nick,
            u.email
        FROM pedidos p
        LEFT JOIN usuarios u ON u.id = p.usuario_id
        WHERE p.id = :id
    ");

    $stmt->execute([':id' => $id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        http_response_code(404);
        exit('Pedido no encontrado.');
    }

    $stmt = $pdo->prepare("
        SELECT
            producto,
            precio,
            cantidad,
            subtotal
        FROM pedido_productos
        WHERE pedido_id = :id
        ORDER BY id ASC
    ");

    $stmt->execute([':id' => $id]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('No se pudo cargar el pedido.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pedido #<?php echo $id; ?> - LunarMC</title>

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

                <h1>Pedido <span>#<?php echo $id; ?></span></h1>
                <p>Información de la compra registrada.</p>
            </div>

        </div>

        <button
            type="button"
            id="admin-theme-toggle"
            class="admin-theme-toggle"
        >
            ☀️ Modo claro
        </button>
    </header>

    <section class="products-panel orders-detail">

        <h2>Información del pedido</h2>

        <div class="orders-detail-grid">

            <div>
                <span>Jugador</span>
                <strong>
                    <?php echo escapar(
                        $pedido['nick'] ?? 'Usuario no disponible'
                    ); ?>
                </strong>
            </div>

            <div>
                <span>Correo electrónico</span>
                <strong>
                    <?php echo escapar(
                        $pedido['email'] ?? 'No disponible'
                    ); ?>
                </strong>
            </div>

            <div>
                <span>Estado</span>
                <strong><?php echo escapar($pedido['estado']); ?></strong>
            </div>

            <div>
                <span>Fecha</span>
                <strong><?php echo escapar($pedido['fecha']); ?></strong>
            </div>

        </div>

        <?php if (isset($_GET['actualizado'])): ?>
            <p role="status" style="color:#4ade80;font-weight:700">Estado actualizado correctamente.</p>
        <?php endif; ?>
        <?php if ($mensajeError !== ''): ?>
            <p role="alert" style="color:#fb7185;font-weight:700"><?php echo escapar($mensajeError); ?></p>
        <?php endif; ?>

        <div class="orders-detail-grid" style="margin:20px 0">
            <div style="grid-column:1/-1">
                <h2>Gestionar estado del pedido</h2>
                <p>Los cambios son manuales. Marcar «Pagado» no comprueba automáticamente un cobro.</p>
                <?php
                $estadoActual = (string)$pedido['estado'];
                $opciones = [
                    'pendiente' => ['pagado' => 'Pagado (pago verificado)', 'cancelado' => 'Cancelado'],
                    'pagado' => ['entregado' => 'Entregado', 'cancelado' => 'Cancelado'],
                ];
                ?>
                <?php if (!empty($opciones[$estadoActual])): ?>
                    <form method="post" action="ver.php?id=<?php echo (int)$id; ?>" style="display:grid;gap:12px;max-width:460px;margin-top:12px">
                        <input type="hidden" name="csrf_token" value="<?php echo escapar($_SESSION['csrf_pedidos']); ?>">
                        <label for="estado-pedido">Nuevo estado</label>
                        <select name="estado" id="estado-pedido" required style="padding:10px;border-radius:8px">
                            <option value="">Seleccioná un estado</option>
                            <?php foreach ($opciones[$estadoActual] as $valor => $texto): ?>
                                <option value="<?php echo escapar($valor); ?>"><?php echo escapar($texto); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($estadoActual === 'pendiente'): ?>
                            <label><input type="checkbox" name="confirmacion" value="1"> Verifiqué el cobro por un medio externo si voy a marcar el pedido como pagado.</label>
                        <?php endif; ?>
                        <button type="submit" class="products-btn products-btn-edit" onclick="return confirm('¿Confirmás el cambio de estado?')">Guardar estado</button>
                    </form>
                <?php else: ?>
                    <p>Este pedido está cerrado o tiene un estado que requiere revisión manual.</p>
                <?php endif; ?>
            </div>
        </div>

        <h2>Productos del pedido</h2>

        <div class="orders-table-scroll">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo escapar($producto['producto']); ?></td>

                            <td>
                                $<?php echo number_format(
                                    (float)$producto['precio'], 2, ',', '.'
                                ); ?>
                            </td>

                            <td><?php echo (int)$producto['cantidad']; ?></td>

                            <td>
                                $<?php echo number_format(
                                    (float)$producto['subtotal'], 2, ',', '.'
                                ); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$productos): ?>
                        <tr>
                            <td colspan="4">
                                Este pedido no tiene productos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="orders-total">
            <span>Total registrado</span>
            <strong>
                $<?php echo number_format(
                    (float)$pedido['total'], 2, ',', '.'
                ); ?>
            </strong>
        </div>

        <p class="orders-payment-note">
            Este registro no demuestra que se haya recibido
            un pago real. La confirmación de pagos se
            implementará mediante un proveedor autorizado.
        </p>

    </section>

    <a href="index.php" class="products-back">
        ← 🏠 Volver a pedidos
    </a>

</main>

</body>
</html>