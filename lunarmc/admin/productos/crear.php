
<?php
// TP7 - CREATE: Agregar productos
require_once __DIR__ . '/../verificar_admin.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errores = [];
$codigo = '';
$nombre = '';
$descripcion = '';
$precio = '';
$categoria_id = '';
$activo = 1;

try {
    $stmt = $pdo->query(
        "SELECT id, nombre FROM categorias ORDER BY nombre ASC"
    );
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Error al cargar las categorías.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }

    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $categoria_id = $_POST['categoria_id'] ?? '';
    $activo = $_POST['activo'] ?? '1';

    if ($codigo === '' || mb_strlen($codigo) > 60) {
        $errores[] = 'Ingresá un código válido (máximo 60 caracteres).';
    }

    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'Ingresá un nombre válido (máximo 100 caracteres).';
    }

    if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $precio)
        || (float)$precio <= 0) {
        $errores[] = 'Ingresá un precio válido mayor a cero.';
    }

    $categoria_valida = false;

    foreach ($categorias as $categoria) {
        if ((string)$categoria['id'] === (string)$categoria_id) {
            $categoria_valida = true;
            break;
        }
    }

    if (!$categoria_valida) {
        $errores[] = 'Seleccioná una categoría válida.';
    }

    if (!in_array((string)$activo, ['0', '1'], true)) {
        $errores[] = 'Estado no válido.';
    }

    if (!$errores) {
        try {
            $sql = "INSERT INTO productos
                    (codigo, categoria_id, nombre, descripcion, precio, activo)
                    VALUES
                    (:codigo, :categoria_id, :nombre, :descripcion, :precio, :activo)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':codigo' => $codigo,
                ':categoria_id' => (int)$categoria_id,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion !== '' ? $descripcion : null,
                ':precio' => $precio,
                ':activo' => (int)$activo
            ]);

            header('Location: index.php?estado=creado');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'El código ya existe o la categoría no es válida.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'No se pudo guardar el producto.';
            }
        }
    }
}

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo producto - LunarMC</title>

    <link rel="stylesheet" href="../../index-.css?v=10">
    <script src="../../index-.js?v=10" defer></script>
</head>

<body class="admin-dashboard admin-products">

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

                <h1>Nuevo <span>producto</span></h1>
                <p>Agregá un producto a tu tienda.</p>
            </div>
        </div>

        <button
            type="button"
            id="admin-theme-toggle"
            class="admin-theme-toggle">
            ☀️ Modo claro
        </button>
    </header>

    <section class="products-panel products-form-panel">

        <?php if ($errores): ?>
            <div class="products-alert">
                <?php foreach ($errores as $error): ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="products-form">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php echo e($_SESSION['csrf_token']); ?>"
            >

            <div class="products-form-grid">

                <div class="products-field">
                    <label for="codigo">Código</label>
                    <input
                        id="codigo"
                        type="text"
                        name="codigo"
                        maxlength="60"
                        value="<?php echo e($codigo); ?>"
                        placeholder="Ejemplo: rango_galaxia"
                        required
                    >
                </div>

                <div class="products-field">
                    <label for="nombre">Nombre</label>
                    <input
                        id="nombre"
                        type="text"
                        name="nombre"
                        maxlength="100"
                        value="<?php echo e($nombre); ?>"
                        placeholder="Ejemplo: Rango Galaxia"
                        required
                    >
                </div>

                <div class="products-field">
                    <label for="categoria_id">Categoría</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">Seleccionar categoría</option>

                        <?php foreach ($categorias as $categoria): ?>
                            <option
                                value="<?php echo (int)$categoria['id']; ?>"
                                <?php echo (string)$categoria_id ===
                                    (string)$categoria['id'] ? 'selected' : ''; ?>
                            >
                                <?php echo e($categoria['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="products-field">
                    <label for="precio">Precio</label>
                    <input
                        id="precio"
                        type="number"
                        name="precio"
                        min="0.01"
                        max="99999999.99"
                        step="0.01"
                        value="<?php echo e($precio); ?>"
                        placeholder="9.99"
                        required
                    >
                </div>

                <div class="products-field">
                    <label for="activo">Estado</label>
                    <select id="activo" name="activo">
                        <option value="1" <?php echo (string)$activo === '1' ? 'selected' : ''; ?>>
                            Activo
                        </option>
                        <option value="0" <?php echo (string)$activo === '0' ? 'selected' : ''; ?>>
                            Inactivo
                        </option>
                    </select>
                </div>

                <div class="products-field products-field-full">
                    <label for="descripcion">Descripción</label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        rows="5"
                        placeholder="Describí el producto..."
                    ><?php echo e($descripcion); ?></textarea>
                </div>

            </div>

            <div class="products-form-actions">
                <a href="index.php" class="products-btn products-btn-edit">
                    Cancelar
                </a>

                <button type="submit" class="products-btn products-btn-primary">
                    Guardar producto
                </button>
            </div>

        </form>
    </section>

    <a href="index.php" class="products-back">
        ← Volver a productos
    </a>

</main>

</body>
</html>
