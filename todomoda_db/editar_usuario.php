<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

require_once __DIR__ . '/conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = ($_POST['rol'] ?? 'usuario') === 'admin' ? 'admin' : 'usuario';

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if ($apellido === '') $errores[] = 'El apellido es obligatorio.';
    if ($dni === '' || !preg_match('/^[0-9]{7,8}$/', $dni)) $errores[] = 'El DNI debe tener 7 u 8 números.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingresá un correo válido.';

    if (empty($errores)) {
        try {
            $sql = 'UPDATE usuarios 
                    SET nombre = :nombre, apellido = :apellido, dni = :dni, email = :email, rol = :rol 
                    WHERE id = :id AND archivado_en IS NULL';
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'rol' => $rol,
                'id' => $id
            ]);
            header('Location: admin_usuarios.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'Ese DNI o correo ya pertenece a otro usuario.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'Error al actualizar los datos.';
            }
        }
    }
} else {
    $stmt = $conexion->prepare('SELECT id, nombre, apellido, dni, email, rol FROM usuarios WHERE id = :id AND archivado_en IS NULL LIMIT 1');
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        exit('Usuario no encontrado o archivado.');
    }
    $nombre = $usuario['nombre'];
    $apellido = $usuario['apellido'];
    $dni = $usuario['dni'];
    $email = $usuario['email'];
    $rol = $usuario['rol'];
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario | Admin Todomoda</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">PANEL ADMINISTRATIVO · TODOMODA ARGENTINA</div>
        <nav class="navbar">
            <a href="admin_usuarios.php" class="logo">TODOMODA ADMIN</a>
            <div class="nav-links">
                <a href="admin_usuarios.php">Volver al listado</a>
                <a href="cerrar_sesion.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main class="contenedor-form">
        <h1>Editar Usuario #<?= (int)$id ?></h1>

        <?php if (!empty($errores)): ?>
            <div class="alerta-error">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= escapar($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="editar_usuario.php" method="POST" class="formulario">
            <input type="hidden" name="id" value="<?= (int)$id ?>">

            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" maxlength="80" value="<?= escapar($nombre) ?>" required>

            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" maxlength="80" value="<?= escapar($apellido) ?>" required>

            <label for="dni">DNI</label>
            <input type="text" id="dni" name="dni" pattern="[0-9]{7,8}" maxlength="8" value="<?= escapar($dni) ?>" required>

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" maxlength="254" value="<?= escapar($email) ?>" required>

            <label for="rol">Rol</label>
            <select id="rol" name="rol">
                <option value="usuario" <?= $rol === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= $rol === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>

            <button type="submit" class="btn-negro">GUARDAR CAMBIOS</button>
        </form>
    </main>
</body>
</html>