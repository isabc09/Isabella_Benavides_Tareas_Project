<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

require_once __DIR__ . '/conexion.php';

$sql = 'SELECT id, nombre, apellido, dni, email, rol, archivado_en 
        FROM usuarios 
        WHERE archivado_en IS NOT NULL 
        ORDER BY archivado_en DESC';
$stmt = $conexion->query($sql);
$archivados = $stmt->fetchAll();

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Archivados | Admin Todomoda</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">PANEL ADMINISTRATIVO · TODOMODA ARGENTINA</div>
        <nav class="navbar">
            <a href="admin_usuarios.php" class="logo">TODOMODA ADMIN</a>
            <div class="nav-links">
                <a href="admin_usuarios.php">Usuarios Activos</a>
                <a href="crear_usuario_admin.php">+ Nuevo Usuario</a>
                <a href="usuarios_archivados.php" class="activo">Archivados</a>
                <a href="cerrar_sesion.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main class="contenedor-ancho">
        <h1>Cuentas Archivadas</h1>
        <p class="subtitulo">Total archivados: <strong><?= count($archivados) ?></strong></p>

        <?php if (empty($archivados)): ?>
            <p class="alerta-info">No hay usuarios archivados actualmente.</p>
        <?php else: ?>
            <div class="tabla-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>DNI</th>
                            <th>Correo electrónico</th>
                            <th>Fecha de archivado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($archivados as $u): ?>
                            <tr>
                                <td><?= escapar((string)$u['id']) ?></td>
                                <td><?= escapar($u['nombre']) ?></td>
                                <td><?= escapar($u['apellido']) ?></td>
                                <td><?= escapar($u['dni']) ?></td>
                                <td><?= escapar($u['email']) ?></td>
                                <td><?= escapar($u['archivado_en']) ?></td>
                                <td>
                                    <form action="reactivar_usuario.php" method="POST" onsubmit="return confirm('¿Reactivar esta cuenta?');">
                                        <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                        <button type="submit" class="btn-reactivar">Reactivar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>