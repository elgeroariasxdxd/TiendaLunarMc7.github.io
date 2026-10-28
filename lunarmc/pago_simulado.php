<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Cache-Control: no-store');
if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || (int)($_SESSION['simulacion_pedido_id'] ?? 0) !== $id) {
    http_response_code(403); exit('Pedido no disponible para simulación.');
}
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
try {
    require_once __DIR__ . '/includes/database.php';
    $stmt = $pdo->prepare('SELECT id, total, estado FROM pedidos WHERE id = :id AND usuario_id = :uid LIMIT 1');
    $stmt->execute([':id' => $id, ':uid' => (int)$_SESSION['usuario_id']]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pedido) { http_response_code(404); exit('Pedido no encontrado.'); }
    $stmt = $pdo->prepare('SELECT resultado FROM simulaciones_pago WHERE pedido_id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $previo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('LunarMC simulador: ' . $e->getMessage());
    http_response_code(500); exit('No se pudo consultar el pedido.');
}
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pago de prueba - LunarMC</title><link rel="stylesheet" href="index-.css"><style>.sim-wrap{max-width:650px;margin:80px auto;padding:32px;background:#171b30;border:1px solid #394363;border-radius:18px;color:#f4f5ff;font-family:Arial,sans-serif}.sim-wrap h1{margin:0 0 18px}.sim-alert{background:#342b16;border:1px solid #a47a2c;padding:15px;border-radius:10px;margin:18px 0}.sim-wrap label{display:block;margin:14px 0}.sim-wrap button,.sim-wrap a.sim-link{display:inline-block;padding:13px 22px;background:#5c73cc;color:white;border:0;border-radius:9px;text-decoration:none;cursor:pointer}.sim-wrap button:disabled{opacity:.5}.sim-wrap select{width:100%;padding:12px;border-radius:8px}.sim-wrap small{color:#c8cee6}</style></head><body class="dark-mode"><main class="sim-wrap"><h1>Pago de prueba · LunarMC</h1><div class="sim-alert"><strong>SIMULACIÓN — NO ES MERCADO PAGO.</strong><p>No se cobrará dinero ni se solicitarán datos de tarjetas. No se entregarán rangos.</p></div><p>Pedido #<?= (int)$pedido['id'] ?> · Total de referencia: <strong>$<?= number_format((float)$pedido['total'],2,',','.') ?> USD</strong></p><?php if ($previo): ?><p>Este pedido ya tiene un resultado de simulación: <strong><?=e($previo['resultado'])?></strong>.</p><a class="sim-link" href="resultado_simulado.php?id=<?=$id?>">Ver resultado</a><?php elseif ($pedido['estado'] !== 'pendiente'): ?><p>El pedido no está pendiente. No puede simularse.</p><?php else: ?><form method="post" action="confirmar_simulacion.php"><input type="hidden" name="id" value="<?=$id?>"><input type="hidden" name="csrf_token" value="<?=e($_SESSION['csrf_token'])?>"><label for="metodo">Método ficticio de pago</label><select id="metodo" name="metodo" required><option value="tarjeta">Tarjeta de prueba (sin datos reales)</option><option value="billetera">Billetera virtual ficticia</option></select><label><input type="checkbox" name="acepto" value="1" required> Entiendo que es una simulación sin cobros reales.</label><button type="submit" name="resultado" value="aprobado">Simular pago exitoso</button> <button type="submit" name="resultado" value="rechazado">Simular pago rechazado</button></form><?php endif ?><p><small>El pedido real permanece «pendiente». El resultado de prueba se guarda por separado.</small></p></main></body></html>
