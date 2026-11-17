<?php
session_start();
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

require_once __DIR__ . '/conexion.php';

$errores = [];
$nombre = $apellido = $dni = $email = '';
$rol = 'usuario';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = ($_POST['rol'] ?? 'usuario') === 'admin' ? 'admin' : 'usuario';

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if ($apellido === '') $errores[] = 'El apellido es obligatorio.';
    if ($dni === '' || !preg_match('/^[0-9]{7,8}$/', $dni)) $errores[] = 'El DNI debe tener 7 u 8 números.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingresá un correo válido.';
    if (strlen($password) < 4) $errores[] = 'La contraseña debe tener al menos 4 caracteres.';

    if (empty($errores)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $sql = 'INSERT INTO usuarios (nombre, apellido, dni, email, password_hash, rol) 
                    VALUES (:nombre, :apellido, :dni, :email, :password_hash, :rol)';
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'password_hash' => $passwordHash,
                'rol' => $rol
            ]);
            header('Location: admin_usuarios.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'Ese DNI o correo electrónico ya está registrado.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'Error al registrar el usuario.';
            }
        }
    }
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario | Admin Todomoda</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">PANEL ADMINISTRATIVO · TODOMODA ARGENTINA</div>
        <nav class="navbar">
            <a href="admin_usuarios.php" class="logo">TODOMODA ADMIN</a>
            <div class="nav-links">
                <a href="admin_usuarios.php">Usuarios Activos</a>
                <a href="crear_usuario_admin.php" class="activo">+ Nuevo Usuario</a>
                <a href="usuarios_archivados.php">Archivados</a>
                <a href="cerrar_sesion.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main class="contenedor-form">
        <h1>Registrar Nuevo Usuario</h1>

        <?php if (!empty($errores)): ?>
            <div class="alerta-error">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= escapar($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="crear_usuario_admin.php" method="POST" class="formulario">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" maxlength="80" value="<?= escapar($nombre) ?>" required>

            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" maxlength="80" value="<?= escapar($apellido) ?>" required>

            <label for="dni">DNI</label>
            <input type="text" id="dni" name="dni" pattern="[0-9]{7,8}" maxlength="8" value="<?= escapar($dni) ?>" required>

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" maxlength="254" value="<?= escapar($email) ?>" required>

            <label for="password">Contraseña inicial</label>
            <input type="password" id="password" name="password" required>

            <label for="rol">Rol del usuario</label>
            <select id="rol" name="rol">
                <option value="usuario" <?= $rol === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= $rol === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>

            <button type="submit" class="btn-negro">GUARDAR USUARIO</button>
        </form>
    </main>
</body>
</html>