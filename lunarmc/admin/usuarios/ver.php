
<?php
require_once __DIR__ . '/../verificar_admin.php';

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de usuario inválido.');
}

try {
    // Obtener los datos del usuario y su rol
    $stmt = $pdo->prepare("
        SELECT
            u.id,
            u.nick,
            u.email,
            u.gender,
            u.fecha_registro,
            r.nombre AS rol_nombre
        FROM usuarios u
        LEFT JOIN roles r ON r.id = u.rol_id
        WHERE u.id = :id
    ");

    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404);
        exit('Usuario no encontrado.');
    }

    // Obtener historial de pedidos
    $stmt = $pdo->prepare("
        SELECT
            id,
            total,
            estado,
            fecha
        FROM pedidos
        WHERE usuario_id = :id
        ORDER BY fecha DESC, id DESC
    ");

    $stmt->execute([':id' => $id]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPedidos = count($pedidos);
    $importeRegistrado = array_sum(
        array_map(
            fn($pedido) => (float)$pedido['total'],
            $pedidos
        )
    );

} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('No se pudo cargar la información del usuario.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= escapar($usuario['nick']) ?> - LunarMC
    </title>

    <link rel="stylesheet" href="../../index-.css?v=14">
    <script src="../../index-.js?v=14" defer></script>
</head>

<body class="admin-dashboard admin-products admin-orders admin-users">

<main class="products-container">

    <header class="products-header">
        <div class="products-heading">
          <img
        src="https://mc-heads.net/body/<?= rawurlencode($usuario['nick']) ?>/150"
     alt="Skin de <?= escapar($usuario['nick']) ?>"
     class="users-minecraft-skin"
     loading="lazy"
     referrerpolicy="no-referrer"
>

            <div>
                <span class="products-eyebrow">
                    LUNARMC / ADMINISTRACIÓN
                </span>

                <h1>Perfil de <span><?= escapar($usuario['nick']) ?></span></h1>

                <p>Información de la cuenta e historial de pedidos.</p>
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

        <h2>Información del usuario</h2>

        <div class="orders-detail-grid">
            <div>
                <span>ID de usuario</span>
                <strong>#<?= (int)$usuario['id'] ?></strong>
            </div>

            <div>
                <span>Nombre de Minecraft</span>
                <strong><?= escapar($usuario['nick']) ?></strong>
            </div>

            <div>
                <span>Correo electrónico</span>
                <strong><?= escapar($usuario['email']) ?></strong>
            </div>

            <div>
                <span>Rol</span>
                <strong><?= escapar($usuario['rol_nombre'] ?? 'Sin rol') ?></strong>
            </div>

            <div>
                <span>Fecha de registro</span>
                <strong><?= escapar($usuario['fecha_registro']) ?></strong>
            </div>

            <div>
                <span>Pedidos registrados</span>
                <strong><?= $totalPedidos ?></strong>
            </div>
        </div>

        <h2>Historial de pedidos</h2>

        <div class="orders-table-scroll">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php
                            $estado = strtolower((string)$pedido['estado']);

                            $claseEstado = match ($estado) {
                                'pendiente' => 'pending',
                                'pagado', 'aprobado' => 'paid',
                                'cancelado', 'rechazado' => 'cancelled',
                                default => 'other'
                            };
                        ?>

                        <tr>
                            <td>#<?= (int)$pedido['id'] ?></td>

                            <td><?= escapar($pedido['fecha']) ?></td>

                            <td>
                                $<?= number_format(
                                    (float)$pedido['total'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </td>

                            <td>
                                <span class="orders-status orders-status-<?= $claseEstado ?>">
                                    <?= escapar(ucfirst($estado)) ?>
                                </span>
                            </td>

                            <td>
                                <a
                                    href="../pedidos/ver.php?id=<?= (int)$pedido['id'] ?>"
                                    class="products-btn products-btn-edit"
                                >
                                    Ver pedido
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$pedidos): ?>
                        <tr>
                            <td colspan="5">
                                Este usuario todavía no tiene pedidos.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="orders-total">
            <span>Importe total registrado en pedidos</span>
            <strong>
                $<?= number_format($importeRegistrado, 2, ',', '.') ?>
            </strong>
        </div>

        <p class="orders-payment-note">
            Los importes corresponden a pedidos registrados,
            no necesariamente a pagos recibidos.
        </p>

    </section>

    <a href="index.php" class="products-back">
        🏠 Volver a usuarios
    </a>

</main>

</body>
</html>
