<?php
// TP7 - READ: categorias con PDO. Solo administradores.
require_once __DIR__ . '/../verificar_admin.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $stmt = $pdo->prepare('SELECT id, nombre FROM categorias ORDER BY id ASC');
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Categorias LunarMC: ' . $e->getMessage());
    http_response_code(500);
    exit('Error al consultar las categorías.');
}

$mensajes = [
    'creada' => 'Categoría creada correctamente.',
    'editada' => 'Categoría actualizada correctamente.',
    'eliminada' => 'Categoría eliminada correctamente.'
];
$mensaje = $mensajes[$_GET['estado'] ?? ''] ?? '';
// Compatibilidad con redirecciones existentes.
if (!$mensaje && isset($_GET['eliminada']) && $_GET['eliminada'] === '1') {
    $mensaje = 'Categoría eliminada correctamente.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | LunarMC Admin</title>
    <link rel="stylesheet" href="../../index-.css?v=20261024-5">
    <script src="../../index-.js?v=20261024-5" defer></script>
</head>
<body class="admin-dashboard categorias-page">
    <div class="cat-shell">
        <header class="cat-header">
            <a href="../index.php" class="cat-brand">
                <img src="../../imagen del servidor lunar.png" alt="Logo de LunarMC">
                <span><strong>LunarMC</strong><small>ADMINISTRACIÓN</small></span>
            </a>
            <div class="cat-header-actions">

    <div class="admin-header-actions">

        <?php require __DIR__ . '/../tarjeta_admin.php'; ?>

        <button
            type="button"
            id="admin-theme-toggle"
            class="admin-theme-toggle"
            aria-pressed="false"
        >
            ☀️ Modo claro
        </button>

    </div>

   
</div>
        </header>

        <main class="cat-main">
            <div class="cat-heading">
                <div>
                    <span class="cat-eyebrow">PANEL DE ADMINISTRACIÓN / CATEGORÍAS</span>
                    <h1>Gestión de <span>categorías</span></h1>
                    <p>Creá, consultá, editá y eliminá categorías de LunarMC.</p>
                </div>
                <a href="crear.php" class="cat-button">+ Nueva categoría</a>
            </div>

            <?php if ($mensaje): ?>
                <div class="cat-alert" role="status"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <section class="cat-card" aria-label="Listado de categorías">
                <div class="cat-card-top">
                    <h2>Categorías registradas</h2>
                    <span class="cat-count"><?php echo count($categorias); ?> en total</span>
                </div>
                <div class="cat-table-wrap">
                    <table class="cat-table">
                        <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
                        <tbody>
                        <?php if (!$categorias): ?>
                            <tr><td colspan="3" class="cat-empty">Todavía no hay categorías registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><span class="cat-id">#<?php echo (int)$categoria['id']; ?></span></td>
                                <td><strong><?php echo htmlspecialchars($categoria['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td>
                                    <div class="cat-actions">
                                        <a class="cat-button cat-edit" href="editar.php?id=<?php echo (int)$categoria['id']; ?>">Editar</a>
                                        <form method="POST" action="eliminar.php" class="cat-delete-form" onsubmit="return confirm('¿Seguro que querés eliminar esta categoría?');">
                                            <input type="hidden" name="id" value="<?php echo (int)$categoria['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <button class="cat-button cat-delete" type="submit">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <p class="cat-footnote">Las categorías asociadas a productos deben conservar su integridad referencial.</p>
            <div class="cat-bottom-nav">
    <a href="../index.php" class="cat-bottom-back">
         ← 🏠 Volver al panel
    </a>
</div>
        </main>
    </div>
</body>
</html>