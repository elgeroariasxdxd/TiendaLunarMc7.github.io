
<?php
// LunarMC - Control de acceso de administradores

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que haya iniciado sesion
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit;
}

// Usar la conexion PDO existente
require_once __DIR__ . '/../includes/database.php';

try {
    // Consultar el rol real del usuario en MySQL
    $sql = "SELECT rol_id
            FROM usuarios
            WHERE id = :id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $_SESSION['usuario_id']
    ]);

    $usuario = $stmt->fetch();

    // Solo permitir administradores (rol_id = 2)
    if (!$usuario || (int)$usuario['rol_id'] !== 2) {
        http_response_code(403);
        exit('Acceso denegado. No tenes permisos de administrador.');
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Error al verificar los permisos.');
}
