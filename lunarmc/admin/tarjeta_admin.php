<?php
$adminNick = $_SESSION['nick'] ?? 'Administrador';

$adminNickSeguro = htmlspecialchars(
    $adminNick,
    ENT_QUOTES,
    'UTF-8'
);

$adminSkin = 'https://mc-heads.net/avatar/'
    . rawurlencode($adminNick)
    . '/64';
?>

<div class="admin-profile">
    <img
        src="<?php echo htmlspecialchars($adminSkin, ENT_QUOTES, 'UTF-8'); ?>"
        alt="Skin del administrador"
        class="admin-profile-avatar"
        loading="lazy"
    >

    <div class="admin-profile-info">
        <span class="admin-profile-role">
            <span>✦</span> ADMINISTRADOR
        </span>

        <strong><?php echo $adminNickSeguro; ?></strong>
    </div>

    <span class="admin-profile-arrow">⌄</span>

    <div class="admin-profile-menu">
        <a href="/lunarmc/admin/index.php">
            ▦ Panel de administración
        </a>

        <a href="/lunarmc/panel.php">
            ♙ Mi cuenta
        </a>

        <a href="/lunarmc/index.php">
            ↗ Volver a la tienda
        </a>
    </div>
</div>