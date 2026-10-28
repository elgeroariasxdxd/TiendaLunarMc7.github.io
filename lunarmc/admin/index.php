<?php
require_once __DIR__ . '/verificar_admin.php';

$nick = $_SESSION['nick'] ?? 'Administrador';
$estadisticas = ['categorias' => 0, 'productos' => 0, 'pedidos' => 0, 'usuarios' => 0];
try {
    foreach ($estadisticas as $tabla => $valor) {
        // Nombres de tabla fijos, nunca provienen de entradas del usuario.
        $estadisticas[$tabla] = (int) $pdo->query("SELECT COUNT(*) FROM {$tabla}")->fetchColumn();
    }
} catch (PDOException $e) {
    error_log('Dashboard LunarMC: ' . $e->getMessage());
    $errorEstadisticas = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración | LunarMC</title>
    <link rel="stylesheet" href="../index-.css?v=20261024-4">
    <script src="../index-.js?v=2" defer></script>
</head>
<body class="admin-dashboard">
<div class="dashboard">
    <aside class="sidebar">
        <a class="brand" href="../index.php" aria-label="LunarMC, ir a la tienda">
            <img class="brand-logo" src="../imagen del servidor lunar.png" alt="Banner original de LunarMC">
            <span><strong>LUNAR<span>MC</span></strong><small>NETWORK · ADMIN</small></span>
        </a>
        <div class="sidebar-label">MENÚ PRINCIPAL</div>
        <nav class="menu" aria-label="Administración">
            <a class="active" href="index.php">▦ <span>Resumen</span></a>
            <a href="categorias/index.php">▤ <span>Categorías</span></a>
            <a href="productos/index.php">◈ <span>Productos</span></a>
            <a href="pedidos/index.php">▣ <span>Pedidos</span></a>
            <a href="usuarios/index.php">♙ <span>Usuarios</span></a>
       </nav>

<div class="sidebar-bottom">
    <button
        type="button"
        id="admin-theme-toggle"
        class="admin-theme-toggle"
        aria-pressed="false"
    >
        ☀️ Modo claro
    </button>

    <a href="../panel.php">← Volver a mi cuenta</a>
    <a href="../index.php">↗ Ver tienda</a>
</div>

</aside>
    <main class="content">
        
        <header class="topbar">
    <span class="breadcrumb">
        LunarMC <span>/</span> Administración <span>/</span> Resumen
    </span>

    <?php require __DIR__ . '/tarjeta_admin.php'; ?>
</header>
        <?php if (!empty($errorEstadisticas)): ?><p class="notice">No se pudieron cargar algunas estadísticas. Revisá la conexión con la base de datos.</p><?php endif; ?>
        <section class="stats" aria-label="Estadísticas">
            <article class="stat"><span class="stat-icon purple">▤</span><span class="stat-label">Categorías</span><strong><?php echo $estadisticas['categorias']; ?></strong><small>Registradas</small></article>
            <article class="stat"><span class="stat-icon blue">◈</span><span class="stat-label">Productos</span><strong><?php echo $estadisticas['productos']; ?></strong><small>En el catálogo</small></article>
            <article class="stat"><span class="stat-icon gold">▣</span><span class="stat-label">Pedidos</span><strong><?php echo $estadisticas['pedidos']; ?></strong><small>En la base de datos</small></article>
            <article class="stat"><span class="stat-icon green">♙</span><span class="stat-label">Usuarios</span><strong><?php echo $estadisticas['usuarios']; ?></strong><small>Registrados</small></article>
        </section>
        <section class="section-head"><div><span class="eyebrow">HERRAMIENTAS</span><h2>Gestión de la tienda</h2><p>Accedé a los módulos de administración.</p></div></section>
        <section class="modules">
            <article class="module featured"><div class="module-icon">📁</div><span class="status ready">DISPONIBLE</span><h3>Categorías</h3><p>Creá, consultá, editá y eliminá categorías mediante el CRUD con PDO.</p><a class="action" href="categorias/index.php">Gestionar categorías <span>→</span></a></article>
            <article class="module featured"><div class="module-icon">📦</div><span class="status ready">DISPONIBLE</span><h3>Productos</h3><p>Creá, consultá, editá y eliminá rangos, extras y productos de LunarMC mediante PDO.</p><a class="action" href="productos/index.php">Gestionar productos <span>→</span></a></article>
            <article class="module featured"><div class="module-icon">🛒</div><span class="status ready">DISPONIBLE</span><h3>Pedidos</h3><p>Consultá el historial de compras, los productos adquiridos y el estado de cada pedido.</p><a class="action" href="pedidos/index.php">Gestionar pedidos <span>→</span></a></article>
            <article class="module featured"><div class="module-icon">👥</div><span class="status ready">DISPONIBLE</span><h3>Usuarios</h3><p>Consultá las cuentas registradas, buscá jugadores y revisá sus perfiles e historial de pedidos.</p><a class="action" href="usuarios/index.php">Gestionar usuarios <span>→</span></a></article>
        </section>
        <footer class="footer">LUNARMC NETWORK <span>·</span> Panel privado de administración</footer>
    </main>
</div>

</body>
</html>