<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado. Solo administradores.');
}

require_once __DIR__ . '/conexion.php';

$buscar = trim($_GET['buscar'] ?? '');

$sql = 'SELECT id, nombre, apellido, dni, email, rol, creado_en 
        FROM usuarios 
        WHERE archivado_en IS NULL';
$parametros = [];

if ($buscar !== '') {
    $sql .= ' AND (nombre LIKE :nombre OR apellido LIKE :apellido OR dni LIKE :dni OR email LIKE :email)';
    $termino = '%' . $buscar . '%';
    $parametros = [
        'nombre' => $termino,
        'apellido' => $termino,
        'dni' => $termino,
        'email' => $termino
    ];
}

$sql .= ' ORDER BY id DESC';
$stmt = $conexion->prepare($sql);
$stmt->execute($parametros);
$usuarios = $stmt->fetchAll();

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Usuarios | Todomoda</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">PANEL ADMINISTRATIVO · TODOMODA ARGENTINA</div>
        <nav class="navbar">
            <a href="admin_usuarios.php" class="logo">TODOMODA ADMIN</a>
            <div class="nav-links">
                <a href="admin_usuarios.php" class="activo">Usuarios Activos</a>
                <a href="crear_usuario_admin.php">+ Nuevo Usuario</a>
                <a href="usuarios_archivados.php">Archivados</a>
                <a href="panel.php">Vista Usuario</a>
                <a href="cerrar_sesion.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main class="contenedor-ancho">
        <div class="cabecera-admin">
            <div>
                <h1>Gestión de Usuarios Activos</h1>
                <p class="subtitulo">Total encontrados: <strong><?= count($usuarios) ?></strong></p>
            </div>

            <form method="GET" action="admin_usuarios.php" class="form-buscador">
                <input type="search" name="buscar" maxlength="100" value="<?= escapar($buscar) ?>" placeholder="Nombre, apellido, DNI o correo...">
                <button type="submit" class="btn-negro-chico">Buscar</button>
                <?php if ($buscar !== ''): ?>
                    <a href="admin_usuarios.php" class="btn-borde">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($usuarios)): ?>
            <p class="alerta-info">No se encontraron usuarios activos con ese criterio.</p>
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
                            <th>Rol</th>
                            <th>Alta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?= escapar((string)$u['id']) ?></td>
                                <td><?= escapar($u['nombre']) ?></td>
                                <td><?= escapar($u['apellido']) ?></td>
                                <td><?= escapar($u['dni']) ?></td>
                                <td><?= escapar($u['email']) ?></td>
                                <td><span class="badge"><?= escapar($u['rol']) ?></span></td>
                                <td><?= escapar($u['creado_en']) ?></td>
                                <td class="acciones-celda">
                                    <a href="editar_usuario.php?id=<?= (int)$u['id'] ?>" class="btn-editar">Editar</a>

                                    <?php if ((int)$u['id'] !== (int)$_SESSION['usuario_id']): ?>
                                        <form action="archivar_usuario.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que querés archivar esta cuenta?');">
                                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                            <button type="submit" class="btn-archivar">Archivar</button>
                                        </form>
                                    <?php endif; ?>
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