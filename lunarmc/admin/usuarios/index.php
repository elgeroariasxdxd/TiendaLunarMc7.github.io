
<?php
require_once __DIR__ . '/../verificar_admin.php';

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

try {
    $stmt = $pdo->query("
        SELECT
            u.id,
            u.nick,
            u.email,
            u.gender,
            u.fecha_registro,
            u.rol_id,
            r.nombre AS rol_nombre,
            (
                SELECT COUNT(*)
                FROM pedidos p
                WHERE p.usuario_id = u.id
            ) AS cantidad_pedidos
        FROM usuarios u
        LEFT JOIN roles r ON r.id = u.rol_id
        ORDER BY u.id DESC
    ");

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('No se pudieron cargar los usuarios.');
}

$totalUsuarios = count($usuarios);
$totalAdministradores = 0;

foreach ($usuarios as $usuario) {
    if (strcasecmp((string)$usuario['rol_nombre'], 'admin') === 0
        || strcasecmp((string)$usuario['rol_nombre'], 'administrador') === 0) {
        $totalAdministradores++;
    }
}

$totalJugadores = $totalUsuarios - $totalAdministradores;

$busqueda = trim((string)($_GET['buscar'] ?? ''));

if ($busqueda !== '') {
    $usuarios = array_values(array_filter(
        $usuarios,
        function ($usuario) use ($busqueda) {
            return stripos($usuario['nick'], $busqueda) !== false
                || stripos($usuario['email'], $busqueda) !== false;
        }
    ));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de usuarios - LunarMC</title>

    <link rel="stylesheet" href="../../index-.css?v=13">
    <script src="../../index-.js?v=13" defer></script>
</head>

<body class="admin-dashboard admin-products admin-orders admin-users">

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

                <h1>Gestión de <span>usuarios</span></h1>
                <p>Consultá las cuentas registradas en LunarMC.</p>
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

    <section class="orders-stats">
        <article class="orders-stat">
            <span>Usuarios registrados</span>
            <strong><?= $totalUsuarios ?></strong>
        </article>

        <article class="orders-stat">
            <span>Administradores</span>
            <strong><?= $totalAdministradores ?></strong>
        </article>

        <article class="orders-stat">
            <span>Otras cuentas</span>
            <strong><?= $totalJugadores ?></strong>
        </article>
    </section>

    <section class="products-panel">

        <div class="orders-heading">
            <h2>Usuarios registrados</h2>
            <p>Buscá jugadores y consultá sus datos.</p>
        </div>

        <form method="GET" class="users-search-form">
            <input
                type="search"
                name="buscar"
                placeholder="Buscar por nick o correo..."
                value="<?= escapar($busqueda) ?>"
                maxlength="150"
            >

            <button type="submit" class="products-btn users-btn-search">
    <svg viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="11" cy="11" r="7"></circle>
        <path d="m20 20-4-4"></path>
    </svg>
    Buscar
</button>

<a href="index.php" class="products-btn users-btn-clear">
    <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M3 11a9 9 0 1 1 2.6 6.4"></path>
        <path d="M3 4v7h7"></path>
    </svg>
    Limpiar
</a>
        </form>

        <div class="orders-table-scroll">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nick</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Registro</th>
                        <th>Pedidos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= (int)$usuario['id'] ?></td>

                            <td>
    <div class="users-player">
        <img
            src="https://mc-heads.net/avatar/<?= rawurlencode($usuario['nick']) ?>/48"
            alt="Cabeza de <?= escapar($usuario['nick']) ?>"
            class="users-player-avatar"
            loading="lazy"
            referrerpolicy="no-referrer"
        >

        <div class="users-player-info">
            <strong><?= escapar($usuario['nick']) ?></strong>
            <small>Jugador de Minecraft</small>
        </div>
    </div>
</td>

                            <td><?= escapar($usuario['email']) ?></td>

                            <td>
                                <span class="users-role">
                                    <?= escapar($usuario['rol_nombre'] ?? 'Sin rol') ?>
                                </span>
                            </td>

                            <td><?= escapar($usuario['fecha_registro']) ?></td>

                            <td><?= (int)$usuario['cantidad_pedidos'] ?></td>

                            <td>
                                <a
                                    href="ver.php?id=<?= (int)$usuario['id'] ?>"
                                    class="products-btn products-btn-edit"
                                >
                                    Ver detalles
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$usuarios): ?>
                        <tr>
                            <td colspan="7">
                                No se encontraron usuarios.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </section>

    <a href="../index.php" class="products-back">
        🏠 Volver al panel
    </a>

</main>
</body>
</html>
