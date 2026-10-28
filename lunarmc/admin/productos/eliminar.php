
<?php
// TP7 - DELETE: Eliminar productos LunarMC

require_once __DIR__ . '/../verificar_admin.php';

// Solo permitir solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// Verificar token CSRF
$token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

// Validar ID
$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de producto inválido.');
}

try {
    // Verificar si el producto existe
    $stmt = $pdo->prepare(
        "SELECT id FROM productos WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);

    if (!$stmt->fetch()) {
        http_response_code(404);
        exit('Producto no encontrado.');
    }

    // Eliminar el producto
    $stmt = $pdo->prepare(
        "DELETE FROM productos WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);

    header('Location: index.php?estado=eliminado');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    // Puede existir una referencia al producto en pedidos anteriores
    if ($e->getCode() === '23000') {
        header('Location: index.php?estado=producto_en_uso');
        exit;
    }

    http_response_code(500);
    exit('No se pudo eliminar el producto.');
}
