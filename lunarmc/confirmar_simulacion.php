<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método no permitido'); }
if (empty($_SESSION['usuario_id']) || empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], (string)($_POST['csrf_token'] ?? ''))) {
    http_response_code(403); exit('Solicitud no válida');
}
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$metodo = (string)($_POST['metodo'] ?? '');
$resultado = (string)($_POST['resultado'] ?? '');
if (!$id || (int)($_SESSION['simulacion_pedido_id'] ?? 0) !== $id ||
    !in_array($metodo, ['tarjeta', 'billetera'], true) ||
    !in_array($resultado, ['aprobado', 'rechazado'], true) ||
    ($_POST['acepto'] ?? '') !== '1') {
    http_response_code(400); exit('Datos de simulación inválidos');
}
try {
    require_once __DIR__ . '/includes/database.php';
    $pdo->beginTransaction();
    $stmt = $pdo->prepare(
        "SELECT id FROM pedidos WHERE id = :id AND usuario_id = :uid AND estado = 'pendiente' LIMIT 1 FOR UPDATE"
    );
    $stmt->execute([':id' => $id, ':uid' => (int)$_SESSION['usuario_id']]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdo->rollBack(); http_response_code(409); exit('Pedido no disponible para simulación');
    }
    $stmt = $pdo->prepare(
        'INSERT INTO simulaciones_pago (pedido_id, metodo, resultado) VALUES (:id, :metodo, :resultado)'
    );
    $stmt->execute([':id' => $id, ':metodo' => $metodo, ':resultado' => $resultado]);
    $pdo->commit();
    header('Location: resultado_simulado.php?id=' . $id, true, 303);
    exit;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    error_log('LunarMC simulación: ' . $e->getMessage());
    http_response_code(409);
    exit('No se pudo registrar la simulación. Es posible que este pedido ya tenga un resultado.');
}
