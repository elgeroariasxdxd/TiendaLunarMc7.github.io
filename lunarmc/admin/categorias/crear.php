
<?php
// TP7 - CREATE: Crear categorias

require_once __DIR__ . '/../verificar_admin.php';

// Crear token CSRF para proteger el formulario
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$nombre = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Solicitud no autorizada.');
    }

    $nombre = trim($_POST['nombre'] ?? '');

    if ($nombre === '') {
        $error = 'Ingresá el nombre de la categoría.';
    } elseif (mb_strlen($nombre, 'UTF-8') > 100) {
        $error = 'El nombre no puede superar 100 caracteres.';
    } else {
        try {
            // Evitar nombres duplicados
            $stmt = $pdo->prepare(
                "SELECT id FROM categorias
                 WHERE nombre = :nombre
                 LIMIT 1"
            );

            $stmt->execute([
                ':nombre' => $nombre
            ]);

            if ($stmt->fetch()) {
                $error = 'Esa categoría ya existe.';
            } else {
                // CREATE con PDO y consulta preparada
                $stmt = $pdo->prepare(
                    "INSERT INTO categorias (nombre)
                     VALUES (:nombre)"
                );

                $stmt->execute([
                    ':nombre' => $nombre
                ]);

                header('Location: index.php?creada=1');
                exit;
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo crear la categoría.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Nueva categoría - LunarMC</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(
                    circle at top right,
                    #251344,
                    transparent 45%
                ),
                #090b14;
            color: white;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 550px;
            margin: 100px auto;
            padding: 30px;
            background: #1b2030;
            border: 1px solid #353b51;
            border-radius: 14px;
        }

        h1 {
            color: #c4a2ff;
        }

        p {
            color: #a7adbf;
        }

        label {
            display: block;
            margin: 25px 0 10px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px;
            background: #111522;
            color: white;
            border: 1px solid #454b62;
            border-radius: 8px;
            font-size: 16px;
        }

        input:focus {
            outline: 2px solid #7c3aed;
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
        }

        .btn:hover {
            background: #6d28d9;
        }

        .volver {
            display: inline-block;
            margin-left: 15px;
            color: #c4a2ff;
            text-decoration: none;
        }

        .error {
            padding: 12px;
            background: #47202b;
            color: #ffb4b4;
            border-radius: 8px;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<main class="container">

    <h1>➕ Nueva categoría</h1>

    <p>
        Agregá una nueva categoría a LunarMC.
    </p>

    <?php if ($error !== ''): ?>
        <div class="error">
            <?php echo htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="crear.php">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo htmlspecialchars(
                $_SESSION['csrf_token'],
                ENT_QUOTES,
                'UTF-8'
            ); ?>"
        >

        <label for="nombre">
            Nombre de la categoría
        </label>

        <input
            type="text"
            id="nombre"
            name="nombre"
            maxlength="100"
            required
            value="<?php echo htmlspecialchars(
                $nombre,
                ENT_QUOTES,
                'UTF-8'
            ); ?>"
            placeholder="Ejemplo: Cosméticos"
        >

        <button type="submit" class="btn">
            Guardar categoría
        </button>

        <a href="index.php" class="volver">
            Cancelar
        </a>

    </form>

</main>

</body>
</html>
