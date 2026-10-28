
<?php
// TP7 - READ: Gestión de productos LunarMC
require_once __DIR__ . '/../verificar_admin.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $sql = "SELECT
                p.id,
                p.codigo,
                p.nombre,
                p.descripcion,
                p.precio,
                p.activo,
                c.nombre AS categoria
            FROM productos p
            LEFT JOIN categorias c
                ON p.categoria_id = c.id
            ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Error al consultar los productos.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de productos - LunarMC</title>

    <link rel="stylesheet" href="../../index-.css?v=8">
    <script src="../../index-.js?v=8" defer></script>
</head>

<body class="admin-dashboard admin-products">

    <main class="products-container">

       <header class="products-header">
    <div class="products-heading">

        <img
            src="../../imagen del servidor lunar.png"
            alt="Logo de LunarMC"
            class="products-logo"
        >

        <div>
            <span class="products-eyebrow">
                LUNARMC / ADMINISTRACIÓN
            </span>

                <h1>Gestión de <span>productos</span></h1>

                <p>
                    Administrá los productos de tu tienda.
                </p>
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

        <section class="products-panel">

            <div class="products-toolbar">
                <div>
                    <h2>Productos registrados</h2>
                    <p>
                        Total:
                        <?php echo count($productos); ?>
                        productos
                    </p>
                </div>

                <a href="crear.php" class="products-btn products-btn-primary">
                    + Nuevo producto
                </a>
            </div>

            <div class="products-table-wrap">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="7" class="products-empty">
                                    No hay productos registrados.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td>
                                        <?php echo (int)$producto['id']; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars(
                                            $producto['codigo'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?php echo htmlspecialchars(
                                                $producto['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars(
                                            $producto['categoria'] ?? 'Sin categoría',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        $<?php echo number_format(
                                            (float)$producto['precio'],
                                            2,
                                            ',',
                                            '.'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?php if ((int)$producto['activo'] === 1): ?>
                                            <span class="products-status products-status-active">
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="products-status products-status-inactive">
                                                Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="products-actions">
                                            <a
                                                class="products-btn products-btn-edit"
                                                href="editar.php?id=<?php echo (int)$producto['id']; ?>">
                                                Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="eliminar.php"
                                                class="products-delete-form">

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo (int)$producto['id']; ?>">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?php echo htmlspecialchars(
                                                        $_SESSION['csrf_token'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>">

                                                <button
                                                    type="submit"
                                                    class="products-btn products-btn-delete">
                                                    Eliminar
                                                </button>
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

       <a href="../index.php" class="products-back">
     🏠 Volver al panel
</a>

    </main>
    
<!-- CONFIRMACION PERSONALIZADA LUNARMC -->
<div
    id="products-delete-modal"
    class="products-modal-overlay"
    hidden
>
    <div
        class="products-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="products-modal-title"
        aria-describedby="products-modal-description"
    >
        <div class="products-modal-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 6h18M8 6V4h8v2M5 6l1 15h12l1-15M10 11v6M14 11v6"/>
            </svg>
        </div>

        <h2 id="products-modal-title">
            ¿Eliminar producto?
        </h2>

        <p id="products-modal-description">
            Estás por eliminar
            <strong id="products-modal-name"></strong>.
            Esta acción no se puede deshacer.
        </p>

        <div class="products-modal-actions">
            <button
                type="button"
                id="products-modal-cancel"
                class="products-btn products-btn-edit"
            >
                Cancelar
            </button>

            <button
                type="button"
                id="products-modal-confirm"
                class="products-btn products-btn-delete"
            >
                Sí, eliminar
            </button>
        </div>
    </div>
</div>


</body>
</html>
