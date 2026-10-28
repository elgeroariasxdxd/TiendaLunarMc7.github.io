
<?php
// TP7 - DELETE: Eliminar categorias

require_once __DIR__ . '/../verificar_admin.php';

// Solo permitir solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// Verificar token CSRF
$token = $_POST['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit('Solicitud no autorizada.');
}

// Validar ID
$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID inválido.');
}

try {
    // Verificar si la categoria tiene productos
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM productos
         WHERE categoria_id = :id"
    );

    $stmt->execute([':id' => $id]);

    if ((int)$stmt->fetchColumn() > 0) {
        header('Location: index.php?error=con_productos');
        exit;
    }

    // DELETE mediante consulta preparada
    $stmt = $pdo->prepare(
        "DELETE FROM categorias
         WHERE id = :id"
    );

    $stmt->execute([':id' => $id]);

    header('Location: index.php?eliminada=1');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: index.php?error=eliminar');
    exit;
}
