
<?php
// config.php - Configuración de LunarMC

function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file(
        $path,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);

        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if (!defined($name)) {
            define($name, $value);
        }
    }
}

loadEnv(dirname(__DIR__, 2) . '/lunarmc-private/.env');
// Base de datos predeterminada
if (!defined('DB_NAME')) {
    define('DB_NAME', 'lunarmc');
}
