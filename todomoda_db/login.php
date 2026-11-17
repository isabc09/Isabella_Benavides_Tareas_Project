<?php
session_start();
require_once __DIR__ . '/conexion.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Completá tu correo y contraseña.';
    } else {
        $sql = 'SELECT id, nombre, email, password_hash, rol 
                FROM usuarios 
                WHERE email = :email AND archivado_en IS NULL 
                LIMIT 1';
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario !== false && password_verify($password, $usuario['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            if ($usuario['rol'] === 'admin') {
                header('Location: admin_usuarios.php');
            } else {
                header('Location: panel.php');
            }
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos, o la cuenta se encuentra archivada.';
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
    <title>Iniciar Sesión | Todomoda</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">ENVÍO GRATIS EN COMPRAS SUPERIORES A $45.000 · FRIENDS CLUB</div>
        <nav class="navbar">
            <a href="login.php" class="logo">TODOMODA</a>
            <div class="nav-links">
                <a href="login.php" class="activo">Iniciar Sesión</a>
                <a href="registro.php">Crear Cuenta</a>
            </div>
        </nav>
    </header>

    <main class="contenedor-form">
        <h1>Iniciar Sesión</h1>
        <p class="subtitulo">Ingresá con tu cuenta de Todomoda Argentina.</p>

        <?php if ($error !== ''): ?>
            <div class="alerta-error"><?= escapar($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="formulario">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" maxlength="254" value="<?= escapar($email) ?>" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn-negro">INGRESAR</button>
        </form>

        <p class="texto-pie">¿Todavía no tenés cuenta? <a href="registro.php">Registrate acá</a></p>
    </main>
</body>
</html>