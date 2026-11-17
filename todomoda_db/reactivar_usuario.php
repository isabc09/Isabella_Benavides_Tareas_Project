<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

require_once __DIR__ . '/conexion.php';

$stmt = $conexion->prepare('UPDATE usuarios SET archivado_en = NULL WHERE id = :id AND archivado_en IS NOT NULL');
$stmt->execute(['id' => $id]);

header('Location: usuarios_archivados.php');
exit;
?>