
<?php
// Iniciar la sesión
require_once __DIR__ . '/auth_guard.php';
exigirLogin();

// Comprobar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Datos del usuario guardados en la sesión
$nick = $_SESSION['nick'] ?? '';
$email = $_SESSION['email'] ?? '';
$gender = $_SESSION['gender'] ?? '';
// La preferencia de cuenta se lee de MySQL, no se confía en la sesión.
$tipoCuenta = 'no_premium';
$skinUrl = 'https://mc-heads.net/body/Steve/320';
$skinPremium = false;

// Conexión PDO
require_once __DIR__ . '/includes/database.php';

// Verificar si el usuario es administrador
$esAdmin = false;
$misPedidos = [];
$errorPedidos = false;

try {
    $sql = "SELECT rol_id, tipo_cuenta
            FROM usuarios
            WHERE id = :id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $_SESSION['usuario_id']
    ]);

    $usuario = $stmt->fetch();

    if (!$usuario) {
        // La cuenta ya no existe
        session_unset();
        session_destroy();

        header('Location: login.php');
        exit;
    }

    $esAdmin = (int)$usuario['rol_id'] === 2;
    $tipoCuenta = ($usuario['tipo_cuenta'] ?? '') === 'premium' ? 'premium' : 'no_premium';
    // Historial de pedidos exclusivo del usuario autenticado.
    try {
        $stmtPedidos = $pdo->prepare(
            'SELECT id, total, estado, fecha FROM pedidos WHERE usuario_id = :usuario_id ORDER BY fecha DESC, id DESC LIMIT 20'
        );
        $stmtPedidos->execute([':usuario_id' => (int) $_SESSION['usuario_id']]);
        $misPedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);

        if ($misPedidos) {
            $idsPedidos = array_map('intval', array_column($misPedidos, 'id'));
            $marcadores = implode(',', array_fill(0, count($idsPedidos), '?'));
            $stmtItems = $pdo->prepare(
                "SELECT pedido_id, producto, cantidad FROM pedido_productos WHERE pedido_id IN ($marcadores) ORDER BY id ASC"
            );
            $stmtItems->execute($idsPedidos);
            $itemsPorPedido = [];
            foreach ($stmtItems->fetchAll(PDO::FETCH_ASSOC) as $item) {
                $itemsPorPedido[(int)$item['pedido_id']][] = $item;
            }
            foreach ($misPedidos as &$pedidoHistorial) {
                $pedidoHistorial['items'] = $itemsPorPedido[(int)$pedidoHistorial['id']] ?? [];
            }
            unset($pedidoHistorial);
        }
    } catch (PDOException $ePedidos) {
        error_log('LunarMC historial de compras: ' . $ePedidos->getMessage());
        $errorPedidos = true;
    }

    // La skin 3D se genera a partir de la textura original de Minecraft,
    // no de imágenes renderizadas por tamaño de MC-Heads.
    $skinPremium = $tipoCuenta === 'premium'
        && preg_match('/^[A-Za-z0-9_]{3,16}$/D', (string)$nick) === 1;
    if ($skinPremium) {
        // Imagen de respaldo si falla el renderizador 3D.
        $skinUrl = 'https://minotar.net/armor/body/' . rawurlencode($nick) . '/320.png';
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo verificar la cuenta.');
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

    <title>Mi cuenta - LunarMC</title>

    <link rel="stylesheet" href="index-.css?v=20261025">

    
</head>

<body class="dark-mode">

    <main class="panel-container lunar-profile">

        <section class="panel-box">

            <h1>
                👋 Bienvenido a LunarMC
            </h1>

            <p class="panel-welcome">
                Hola,
                <strong>
                    <?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?>
                </strong>.
                Has iniciado sesión correctamente.
            </p>

            <div class="lunar-profile-grid">
                <div class="lunar-skin-card">
                    <span class="lunar-skin-label">Tu skin de Minecraft</span>
                    <div class="lunar-skin-3d-holder" data-premium="<?php echo $skinPremium ? '1' : '0'; ?>">

    <?php if ($skinPremium): ?>

        <canvas
            id="lunar-skin-3d-canvas"
            width="260"
            height="350"
            hidden
            aria-label="Skin 3D de <?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?>">
        </canvas>

        <p id="lunar-skin-3d-status" class="lunar-skin-status" role="status">
            Cargando personaje 3D...
        </p>

    <?php endif; ?>

</div>
                    <span style="font-size:12px;color:#bfc8df;text-align:center"><?php echo $tipoCuenta === 'premium' ? 'Skin pública del nick (si está disponible)' : 'Skin predeterminada'; ?></span>
                    <strong class="lunar-skin-nick"><?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            <div class="user-info">

                <h2>Información de tu cuenta</h2>

                <div class="user-info-row">
                    <span>Nick de Minecraft</span>

                    <strong>
                        <?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?>
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>Email</span>

                    <strong>
                        <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>Género</span>

                    <strong>
                        <?php echo htmlspecialchars($gender, ENT_QUOTES, 'UTF-8'); ?>
                    </strong>
                </div>

                <div class="user-info-row">
                    <span>Tipo de cuenta</span>
                    <strong><?php echo $tipoCuenta === 'premium' ? 'Prémium' : 'No prémium'; ?></strong>
                </div>

                <?php if ($esAdmin): ?>
                    <div class="user-info-row">
                        <span>Rol</span>
                        <strong>Administrador</strong>
                    </div>
                <?php endif; ?>

            </div>

            </div><!-- /lunar-profile-grid -->
            <section class="lunar-purchases" aria-labelledby="lunar-purchases-title">
                <h2 id="lunar-purchases-title">🛒 Mis compras</h2>
                <p class="lunar-purchases-intro">Consultá tus últimos 20 pedidos y su estado actual.</p>
                <?php if ($errorPedidos): ?>
                    <div class="lunar-purchases-empty">No se pudo cargar el historial en este momento.</div>
                <?php elseif (!$misPedidos): ?>
                    <div class="lunar-purchases-empty">Todavía no tenés pedidos registrados. <a href="rangos.php" style="color:#a5b4fc">Explorar rangos</a></div>
                <?php else: ?>
                    <?php
                    $nombresEstado = ['pendiente' => 'Pendiente', 'pagado' => 'Pagado', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado'];
                    ?>
                    <?php foreach ($misPedidos as $pedidoHistorial): ?>
                        <?php
                        $estadoPedido = (string)($pedidoHistorial['estado'] ?? '');
                        $estadoClase = isset($nombresEstado[$estadoPedido]) ? $estadoPedido : '';
                        $fechaPedido = strtotime((string)($pedidoHistorial['fecha'] ?? ''));
                        ?>
                        <article class="lunar-order">
                            <div class="lunar-order-top">
                                <div>
                                    <div class="lunar-order-title">Pedido #<?php echo (int)$pedidoHistorial['id']; ?></div>
                                    <div class="lunar-order-date"><?php echo $fechaPedido ? date('d/m/Y H:i', $fechaPedido) : 'Fecha no disponible'; ?></div>
                                </div>
                                <span class="lunar-status <?php echo $estadoClase ? 'lunar-status-' . $estadoClase : ''; ?>">
                                    <?php echo htmlspecialchars($nombresEstado[$estadoPedido] ?? 'Sin confirmar', ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                            <div class="lunar-order-items">
                                <?php if (empty($pedidoHistorial['items'])): ?>
                                    <div class="lunar-order-item">Sin detalle de productos disponible.</div>
                                <?php else: ?>
                                    <?php foreach ($pedidoHistorial['items'] as $itemPedido): ?>
                                        <div class="lunar-order-item">
                                            <?php echo (int)$itemPedido['cantidad']; ?> × <?php echo htmlspecialchars((string)$itemPedido['producto'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="lunar-order-amount">Total: US$ <?php echo number_format((float)$pedidoHistorial['total'], 2, ',', '.'); ?></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
                <p class="lunar-purchases-note">Los pagos simulados son pruebas: no confirman un pago real ni entregan rangos. El estado del pedido se administra por separado.</p>
            </section>

            <div class="panel-actions">

                <?php if ($esAdmin): ?>
                    <a
                        href="admin/index.php"
                        class="panel-btn panel-btn-home"
                        style="background: #7c3aed; color: white;"
                    >
                        🛡️ Panel de administración
                    </a>
                <?php endif; ?>

                <a href="index.php" class="panel-btn panel-btn-home">
                    Volver al inicio
                </a>

                <a href="logout.php" class="panel-btn panel-btn-logout">
                    Cerrar sesión
                </a>

            </div>

        </section>

    </main>

    <?php if ($skinPremium): ?>
    <script defer src="https://cdn.jsdelivr.net/npm/skinview3d@3.4.2/bundles/skinview3d.bundle.js"></script>
<script defer src="index-.js?v=20261027-skins3"></script>
    <?php endif; ?>
</body>

</html>
