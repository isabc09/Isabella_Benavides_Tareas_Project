<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/conexion.php';

$stmt = $conexion->prepare('SELECT id, nombre, apellido, dni, email, rol, creado_en FROM usuarios WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: cerrar_sesion.php');
    exit;
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil | Todomoda</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">3 Y 6 CUOTAS SIN INTERÉS · TODOMODA ARGENTINA</div>
        <nav class="navbar">
            <a href="panel.php" class="logo">TODOMODA</a>
            <div class="nav-links">
                <a href="panel.php" class="activo">Mis Datos</a>
                <a href="productos.php">Productos (10)</a>
                <a href="historia.php">Historia en Argentina</a>
                <?php if ($usuario['rol'] === 'admin'): ?>
                    <a href="admin_usuarios.php">Panel Admin</a>
                <?php endif; ?>
                <a href="cerrar_sesion.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main class="contenedor">
        <h1>Hola, <?= escapar($usuario['nombre']) ?> 👋</h1>
        <p class="subtitulo">Estos son tus datos personales registrados en Todomoda.</p>

        <div class="tarjeta-perfil">
            <div class="dato-fila">
                <span class="dato-etiqueta">Nombre completo:</span>
                <span><?= escapar($usuario['nombre'] . ' ' . $usuario['apellido']) ?></span>
            </div>
            <div class="dato-fila">
                <span class="dato-etiqueta">DNI:</span>
                <span><?= escapar($usuario['dni']) ?></span>
            </div>
            <div class="dato-fila">
                <span class="dato-etiqueta">Correo electrónico:</span>
                <span><?= escapar($usuario['email']) ?></span>
            </div>
            <div class="dato-fila">
                <span class="dato-etiqueta">Tipo de cuenta:</span>
                <span class="badge"><?= escapar(strtoupper($usuario['rol'])) ?></span>
            </div>
            <div class="dato-fila">
                <span class="dato-etiqueta">Miembro desde:</span>
                <span><?= escapar($usuario['creado_en']) ?></span>
            </div>
        </div>
    </main>
</body>
</html>
