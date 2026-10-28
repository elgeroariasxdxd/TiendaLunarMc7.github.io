<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/database.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); header('Allow: POST'); exit('Método no permitido.');
}
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)($_POST['csrf_token'] ?? ''))) {
    http_response_code(403); exit('Solicitud de compra no válida.');
}
if (($_POST['confirm_account'] ?? '') !== '1' || ($_POST['confirm_terms'] ?? '') !== '1') {
    http_response_code(400); exit('Debés confirmar la cuenta y los términos.');
}
if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php'); exit;
}
$carrito = $_SESSION['carrito'] ?? [];
if (!is_array($carrito) || !$carrito) {
    header('Location: carrito.php'); exit;
}

try {
    // Validar precios reales antes de generar el pedido.
    $stmtCatalogo = $pdo->prepare(
        'SELECT id, nombre, precio FROM productos WHERE codigo = :codigo AND activo = 1 LIMIT 1'
    );
    $items = [];
    $totalCentavos = 0;
    foreach ($carrito as $codigo => $producto) {
        $cantidad = filter_var($producto['cantidad'] ?? null, FILTER_VALIDATE_INT);
        if ($cantidad === false || $cantidad < 1 || $cantidad > 100) {
            http_response_code(400); exit('Cantidad inválida en el carrito.');
        }
        $stmtCatalogo->execute([':codigo' => (string)$codigo]);
        $catalogo = $stmtCatalogo->fetch(PDO::FETCH_ASSOC);
        if (!$catalogo) {
            http_response_code(409); exit('Un producto ya no está disponible. Volvé a revisar el carrito.');
        }
        $precioCentavos = (int)round((float)$catalogo['precio'] * 100);
        if ($precioCentavos < 0) {
            http_response_code(409); exit('Precio inválido en el catálogo.');
        }
        $totalCentavos += $precioCentavos * $cantidad;
        $items[] = [
            'id' => (int)$catalogo['id'], 'nombre' => (string)$catalogo['nombre'],
            'precio' => $precioCentavos / 100, 'cantidad' => $cantidad,
            'subtotal' => ($precioCentavos * $cantidad) / 100,
        ];
    }

    $pdo->beginTransaction();
    $stmtPedido = $pdo->prepare(
        "INSERT INTO pedidos (usuario_id, total, estado) VALUES (:usuario_id, :total, 'pendiente')"
    );
    $stmtPedido->execute([
        ':usuario_id' => (int)$_SESSION['usuario_id'],
        ':total' => $totalCentavos / 100,
    ]);
    $pedidoId = (int)$pdo->lastInsertId();
    $stmtDetalle = $pdo->prepare(
        'INSERT INTO pedido_productos (pedido_id, producto_id, producto, precio, cantidad, subtotal) '
        . 'VALUES (:pedido_id, :producto_id, :producto, :precio, :cantidad, :subtotal)'
    );
    foreach ($items as $item) {
        $stmtDetalle->execute([
            ':pedido_id' => $pedidoId,
            ':producto_id' => $item['id'],
            ':producto' => $item['nombre'],
            ':precio' => $item['precio'],
            ':cantidad' => $item['cantidad'],
            ':subtotal' => $item['subtotal'],
        ]);
    }
    // La meta mensual no cuenta pedidos pendientes ni simulaciones.
    $pdo->commit();
    unset($_SESSION['carrito']);
    $_SESSION['simulacion_pedido_id'] = $pedidoId;
    header('Location: pago_simulado.php?id=' . $pedidoId, true, 303);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('LunarMC procesar pedido: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo crear el pedido. Intentá nuevamente.');
}
