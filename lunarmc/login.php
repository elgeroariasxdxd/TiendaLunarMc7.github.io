<?php
// TP7: inicio de sesión con PDO, consulta preparada y manejo seguro de errores.
require_once __DIR__ . '/includes/session_segura.php';
require_once __DIR__ . '/includes/database.php';

$mensaje = '';
$tipo_mensaje = '';
$nick = '';
if (($_GET['error'] ?? '') === 'acceso_denegado') {
    $mensaje = 'Iniciá sesión para acceder a esa página.';
    $tipo_mensaje = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = trim((string)($_POST['nick'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($nick === '' || $password === '') {
        $mensaje = 'Completá todos los campos.';
        $tipo_mensaje = 'error';
    } else {
        try {
            $stmt = $pdo->prepare(
                'SELECT id, nick, email, password, gender, rol_id, fecha_registro FROM usuarios WHERE nick = :nick LIMIT 1'
            );
            $stmt->execute([':nick' => $nick]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario && password_verify($password, (string)$usuario['password'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = (int)$usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nick'];
                $_SESSION['rol'] = ((int)$usuario['rol_id'] === 2) ? 'admin' : 'usuario';
                $_SESSION['nick'] = $usuario['nick'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['gender'] = $usuario['gender'];
                header('Location: panel.php');
                exit;
            }
            $mensaje = 'Nick o contraseña incorrectos.';
            $tipo_mensaje = 'error';
        } catch (PDOException $e) {
            error_log('LunarMC login: ' . $e->getMessage());
            $mensaje = 'No se pudo iniciar sesión. Intentá nuevamente.';
            $tipo_mensaje = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Iniciar sesión - LunarMC</title>

    <link rel="stylesheet" href="index-.css?v=20261025">
</head>

<body class="dark-mode">

    <main class="login-container">

        <section class="login-box">

            <h1>Iniciar sesión</h1>

            <p>
                Ingresá a tu cuenta de LunarMC.
            </p>

            <?php if ($mensaje !== ''): ?>

                <div class="login-message <?php echo htmlspecialchars(
                    $tipo_mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>">

                    <?php echo htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                </div>

            <?php endif; ?>

            <form method="POST" action="login.php">

                <div class="form-group">

                    <label for="nick">
                        Nick de Minecraft
                    </label>

                    <input
                        type="text"
                        id="nick"
                        name="nick"
                        maxlength="50"
                        required
                        autocomplete="username"
                        value="<?php echo htmlspecialchars(
                            $nick,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >

                </div>

                <div class="form-group">

                    <label for="password">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >

                </div>

                <button type="submit">
                    Iniciar sesión
                </button>

            </form>

            <p>

                ¿Todavía no tenés una cuenta?

                <a href="registro.php">
                    Registrate
                </a>

            </p>

            <p>

                <a href="index.php">
                    Volver al inicio
                </a>

            </p>

        </section>

    </main>

</body>

</html>



