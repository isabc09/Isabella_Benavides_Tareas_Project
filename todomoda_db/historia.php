<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuestra Historia | Todomoda Argentina</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">3 Y 6 CUOTAS SIN INTERÉS · TODOMODA ARGENTINA</div>
        <nav class="navbar">
            <a href="panel.php" class="logo">TODOMODA</a>
            <div class="nav-links">
                <a href="panel.php">Mis Datos</a>
                <a href="productos.php">Productos (10)</a>
                <a href="historia.php" class="activo">Historia en Argentina</a>
                <?php if (($_SESSION['usuario_rol'] ?? '') === 'admin'): ?>
                    <a href="admin_usuarios.php">Panel Admin</a>
                <?php endif; ?>
                <a href="cerrar_sesion.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main class="contenedor">
        <h1>Historia de Todomoda en Argentina</h1>
        <p class="subtitulo">De un emprendimiento familiar en Buenos Aires a una marca líder en Latinoamérica.</p>

        <div class="bloque-historia">
            <h3>1995 · El nacimiento en Buenos Aires</h3>
            <p>Todomoda nació en Argentina en 1995 de la mano de la familia Castelli, bajo el grupo empresarial <strong>Blue Star Group (BSG)</strong>. La idea original fue revolucionar el mercado local ofreciendo accesorios de moda, bijouterie y complementos con diseño actual a precios accesibles para el público joven.</p>

            <h3>Años 2000 · Expansión nacional y regional</h3>
            <p>Tras consolidar sus primeros locales en las principales avenidas y shoppings de la Ciudad de Buenos Aires y el Gran Buenos Aires, la marca se expandió rápidamente a todas las provincias argentinas y dio el salto internacional hacia México, Chile, Perú y Brasil.</p>

            <h3>Actualidad · Lifestyle, Beauty y Comunidad</h3>
            <p>Hoy Todomoda es una de las marcas de accesorios y lifestyle más grandes de Latinoamérica, con cientos de tiendas físicas y tienda online propia. En Argentina amplió su propuesta incorporando líneas completas de maquillaje vegano, fragancias (Body Splash y Hair Mist), papelería y artículos de cuidado personal bajo una estética fresca y colorida.</p>
        </div>
    </main>
</body>
</html>