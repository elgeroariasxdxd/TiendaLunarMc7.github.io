
<?php
// TP7 - UPDATE: Editar categorias de LunarMC

require_once __DIR__ . '/../verificar_admin.php';

// Validar el ID de la categoria
$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de categoría inválido.');
}

// Token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

try {
    // READ por ID mediante consulta preparada
    $stmt = $pdo->prepare(
        "SELECT id, nombre
         FROM categorias
         WHERE id = :id
         LIMIT 1"
    );

    $stmt->execute([
        ':id' => $id
    ]);

    $categoria = $stmt->fetch();

    if (!$categoria) {
        http_response_code(404);
        exit('La categoría no existe.');
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Error al consultar la categoría.');
}

$nombre = $categoria['nombre'];

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
            // Comprobar que no exista otra categoria
            // con el mismo nombre
            $stmt = $pdo->prepare(
                "SELECT id
                 FROM categorias
                 WHERE nombre = :nombre
                   AND id <> :id
                 LIMIT 1"
            );

            $stmt->execute([
                ':nombre' => $nombre,
                ':id' => $id
            ]);

            if ($stmt->fetch()) {
                $error = 'Ya existe otra categoría con ese nombre.';
            } else {
                // UPDATE mediante PDO
                $stmt = $pdo->prepare(
                    "UPDATE categorias
                     SET nombre = :nombre
                     WHERE id = :id"
                );

                $stmt->execute([
                    ':nombre' => $nombre,
                    ':id' => $id
                ]);

                header('Location: index.php?editada=1');
                exit;
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo actualizar la categoría.';
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

    <title>Editar categoría - LunarMC</title>

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

    <h1>✏️ Editar categoría</h1>

    <p>
        Modificá el nombre de la categoría seleccionada.
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

    <form
        method="POST"
        action="editar.php?id=<?php echo (int)$id; ?>"
    >

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
        >

        <button type="submit" class="btn">
            Guardar cambios
        </button>

        <a href="index.php" class="volver">
            Cancelar
        </a>

    </form>

</main>

</body>
</html>
