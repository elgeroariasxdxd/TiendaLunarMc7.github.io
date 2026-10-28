<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Cache-Control: no-store');
if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(400); exit('Pedido inválido'); }
try {
    require_once __DIR__ . '/includes/database.php';
    $stmt = $pdo->prepare(
        'SELECT s.resultado, s.metodo, p.total FROM simulaciones_pago s '
        . 'INNER JOIN pedidos p ON p.id = s.pedido_id '
        . 'WHERE p.id = :id AND p.usuario_id = :uid LIMIT 1'
    );
    $stmt->execute([':id' => $id, ':uid' => (int)$_SESSION['usuario_id']]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$registro) { http_response_code(404); exit('Simulación no encontrada'); }
} catch (PDOException $e) {
    error_log('LunarMC resultado simulación: ' . $e->getMessage());
    http_response_code(500); exit('No se pudo cargar el resultado');
}
$ok = $registro['resultado'] === 'aprobado';
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Resultado de prueba - LunarMC</title><link rel="stylesheet" href="index-.css"><style>.sim-wrap{max-width:620px;margin:100px auto;padding:35px;background:#171b30;border:1px solid #394363;border-radius:18px;color:white;font-family:Arial,sans-serif;text-align:center}.sim-wrap a{display:inline-block;margin:12px;padding:12px 20px;background:#526bcb;color:white;border-radius:9px;text-decoration:none}.sim-note{padding:15px;background:#332b1b;border-radius:9px}</style></head><body class="dark-mode"><main class="sim-wrap"><h1><?= $ok?'¡Compra de prueba exitosa!':'Pago de prueba rechazado' ?></h1><p>Pedido #<?= (int)$id ?> · $<?=number_format((float)$registro['total'],2,',','.')?> USD</p><p class="sim-note"><strong>SIMULACIÓN, SIN DINERO REAL.</strong> El pedido sigue pendiente en el sistema real. No se entregaron productos ni rangos.</p><a href="rangos.php">Volver a la tienda</a><a href="mi-cuenta.php">Mi cuenta</a></main></body></html>
