
<?php

require_once __DIR__ . '/includes/session_segura.php';
require_once __DIR__ . '/includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

if (empty($_SESSION['csrf_registro']) || !hash_equals($_SESSION['csrf_registro'], (string)($_POST['csrf_token'] ?? ''))) {
    http_response_code(403);
    exit('Solicitud no válida. Volvé al registro e intentá nuevamente.');
}

/* ==========================================
   1. RECIBIR DATOS DEL FORMULARIO
   ========================================== */

$nick = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$gender = trim($_POST['gender'] ?? '');
$tipoCuenta = trim($_POST['tipo_cuenta'] ?? '');

/* ==========================================
   2. VALIDAR DATOS
   ========================================== */

if (
    $nick === '' ||
    $email === '' ||
    $password === '' ||
    $gender === '' ||
    !in_array($tipoCuenta, ['premium', 'no_premium'], true)
) {
    die('Todos los campos son obligatorios.');
}

if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('El correo electrónico no es válido.');
}

if (strlen($password) < 8 || strlen($password) > 72) {
    die('La contraseña debe tener entre 8 y 72 bytes.');
}

if (strlen($nick) > 50) {
    die('El nombre de usuario es demasiado largo.');
}

/* ==========================================
   3. COMPROBAR SI NICK O EMAIL YA EXISTEN
   ========================================== */

$sql = "
    SELECT id
    FROM usuarios
    WHERE nick = :nick
       OR email = :email
    LIMIT 1
";

try {
$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nick' => $nick,
    ':email' => $email
]);

$usuarioExistente = $stmt->fetch();

if ($usuarioExistente) {
    die('El nick o el correo electrónico ya están registrados.');
}

/* ==========================================
   4. ENCRIPTAR LA CONTRASEÑA
   ========================================== */

$passwordHash = password_hash(
    $password,
    PASSWORD_BCRYPT
);

/* ==========================================
   5. GUARDAR USUARIO EN MYSQL
   ========================================== */

// El rol 1 corresponde a "cliente" en LunarMC.

$sql = "
    INSERT INTO usuarios (
        nick,
        email,
        password,
        gender,
        tipo_cuenta,
        rol_id
    )
    VALUES (
        :nick,
        :email,
        :password,
        :gender,
        :tipo_cuenta,
        :rol_id
    )
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':nick' => $nick,
    ':email' => $email,
    ':password' => $passwordHash,
    ':gender' => $gender,
    ':tipo_cuenta' => $tipoCuenta,
    ':rol_id' => 1
]);

    // Evitar reutilizar el token después de registrar una cuenta.
    unset($_SESSION['csrf_registro']);
} catch (PDOException $e) {
    error_log('LunarMC registro: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo completar el registro. Intentá nuevamente.');
}

/* ==========================================
   6. MOSTRAR CONFIRMACIÓN
   ========================================== */

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Registro completado - LunarMC</title>

    <link
        rel="stylesheet"
        href="index-.css"
    >
</head>

<body class="dark-mode">

    <main
        style="
            max-width: 700px;
            margin: 100px auto;
            padding: 40px;
            text-align: center;
        "
    >

        <h1 style="color: #F1C40F;">
            ¡Registro completado!
        </h1>

        <p>
            Tu cuenta fue registrada correctamente.
        </p>

        <p>
            Bienvenido a LunarMC,
            <strong>
                <?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?>
            </strong>.
        </p>

        <br>

        <a
            href="registro.php"
            class="btn-submit-register"
        >
            Volver al registro
        </a>

    </main>

</body>
</html>