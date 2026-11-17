<?php
require_once __DIR__ . '/conexion.php';

$errores = [];
$mensaje = '';
$nombre = '';
$apellido = '';
$dni = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }
    if ($apellido === '') {
        $errores[] = 'El apellido es obligatorio.';
    }
    if ($dni === '' || !preg_match('/^[0-9]{7,8}$/', $dni)) {
        $errores[] = 'El DNI debe tener 7 u 8 números.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresá un correo electrónico válido.';
    }
    if ($password === '' || strlen($password) < 4) {
        $errores[] = 'La contraseña es obligatoria (mínimo 4 caracteres).';
    }
    if ($password !== $confirmar) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

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
                'rol' => 'usuario'
            ]);

            $mensaje = '¡Cuenta creada en Friends Club! Ya podés iniciar sesión.';
            $nombre = $apellido = $dni = $email = '';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'Ese DNI o correo electrónico ya está registrado.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'Ocurrió un error al guardar los datos.';
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
    <title>Registro | Todomoda Friends Club</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">ENVÍO GRATIS EN COMPRAS SUPERIORES A $45.000 · FRIENDS CLUB</div>
        <nav class="navbar">
            <a href="login.php" class="logo">TODOMODA</a>
            <div class="nav-links">
                <a href="login.php">Iniciar Sesión</a>
                <a href="registro.php" class="activo">Crear Cuenta</a>
            </div>
        </nav>
    </header>

    <main class="contenedor-form">
        <h1>Unite a Friends Club</h1>
        <p class="subtitulo">Creá tu cuenta para ver el catálogo y tus beneficios.</p>

        <?php if ($mensaje !== ''): ?>
            <div class="alerta-exito"><?= escapar($mensaje) ?></div>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <div class="alerta-error">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= escapar($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST" class="formulario">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" maxlength="80" value="<?= escapar($nombre) ?>" required>

            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" maxlength="80" value="<?= escapar($apellido) ?>" required>

            <label for="dni">DNI</label>
            <input type="text" id="dni" name="dni" inputmode="numeric" pattern="[0-9]{7,8}" maxlength="8" value="<?= escapar($dni) ?>" required>

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" maxlength="254" value="<?= escapar($email) ?>" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmar">Confirmar contraseña</label>
            <input type="password" id="confirmar" name="confirmar" required>

            <button type="submit" class="btn-negro">CREAR CUENTA</button>
        </form>

        <p class="texto-pie">¿Ya tenés cuenta? <a href="login.php">Iniciá sesión acá</a></p>
    </main>
</body>
</html>