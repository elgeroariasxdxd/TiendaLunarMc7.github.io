<?php
// TP8: guardián reutilizable. Compatible con el esquema actual (rol_id 1=usuario, 2=admin).
require_once __DIR__ . '/includes/session_segura.php';

function exigirLogin(): void {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: login.php?error=acceso_denegado');
        exit;
    }
}

function verificarRol(string $rolRequerido): void {
    exigirLogin();
    require_once __DIR__ . '/includes/database.php';
    global $pdo;
    try {
        $stmt = $pdo->prepare('SELECT rol_id FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int) $_SESSION['usuario_id']]);
        $rolId = $stmt->fetchColumn();
        if ($rolId === false) {
            $_SESSION = [];
            header('Location: login.php?error=acceso_denegado');
            exit;
        }
        $rolActual = ((int) $rolId === 2) ? 'admin' : 'usuario';
        $_SESSION['rol'] = $rolActual;
        if ($rolActual !== $rolRequerido) {
            http_response_code(403);
            exit('403 - Acceso denegado: no tenés permisos para esta página.');
        }
    } catch (PDOException $e) {
        error_log('TP8 guard: ' . $e->getMessage());
        http_response_code(500);
        exit('No se pudieron verificar los permisos.');
    }
}
