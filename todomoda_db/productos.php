<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$productos = [
    ['nombre' => 'POLVO TRANSLÚCIDO ULTIMATTE POWDER', 'precio' => '$ 18.900', 'etiqueta' => 'VEGAN', 'color' => '#f8dce0', 'img' => 'img/prod1.jpg'],
    ['nombre' => 'MÁSCARA DE PESTAÑAS FUN PROOF', 'precio' => '$ 12.900', 'etiqueta' => 'VEGAN', 'color' => '#d5f0ea', 'img' => 'img/prod2.jpg'],
    ['nombre' => 'MÁSCARA DE PESTAÑAS TRANSPARENTE "LIFELONG BESTIE" (CLEAR)', 'precio' => '$ 12.900', 'etiqueta' => 'VEGAN', 'color' => '#f4d9e2', 'img' => 'img/prod3.jpg'],
    ['nombre' => 'MÁSCARA DE PESTAÑAS TRANSPARENTE "LIFELONG BESTIE" (PINK)', 'precio' => '$ 12.900', 'etiqueta' => 'VEGAN', 'color' => '#f7d6df', 'img' => 'img/prod4.jpg'],
    ['nombre' => 'HAIR AND BODY MIST CHERRY', 'precio' => '$ 14.900', 'etiqueta' => 'JUST DROPPED!', 'color' => '#fad4d8', 'img' => 'img/prod5.jpg'],
    ['nombre' => 'HAIR AND BODY MIST POCKET CHERRY', 'precio' => '$ 12.900', 'etiqueta' => 'JUST DROPPED!', 'color' => '#f9d6da', 'img' => 'img/prod6.jpg'],
    ['nombre' => 'BODY SPLASH SOFT GLAZE ERA', 'precio' => '$ 18.000', 'etiqueta' => 'NUEVO', 'color' => '#d4eaf7', 'img' => 'img/prod7.jpg'],
    ['nombre' => 'BODY SPLASH BLOOM YOUR OWN WAY', 'precio' => '$ 18.000', 'etiqueta' => 'NUEVO', 'color' => '#faf2cc', 'img' => 'img/prod8.jpg'],
    ['nombre' => 'LIP GLOSS JUICY SHINE CHERRY', 'precio' => '$ 9.900', 'etiqueta' => 'VEGAN', 'color' => '#f6d8e4', 'img' => 'img/prod9.jpg'],
    ['nombre' => 'SET DE BROCHAS PASTEL ESSENTIALS', 'precio' => '$ 22.500', 'etiqueta' => 'BEST SELLER', 'color' => '#e8def8', 'img' => 'img/prod10.jpg']
];

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos | Todomoda</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header class="topbar">
        <div class="promo-bar">3 Y 6 CUOTAS SIN INTERÉS · TODOMODA ARGENTINA</div>
        <nav class="navbar">
            <a href="panel.php" class="logo">TODOMODA</a>
            <div class="nav-links">
                <a href="panel.php">Mis Datos</a>
                <a href="productos.php" class="activo">Productos (10)</a>
                <a href="historia.php">Historia en Argentina</a>
                <?php if (($_SESSION['usuario_rol'] ?? '') === 'admin'): ?>
                    <a href="admin_usuarios.php">Panel Admin</a>
                <?php endif; ?>
                <a href="cerrar_sesion.php" class="btn-salir">Cerrar Sesión</a>
            </div>
        </nav>
    </header>

    <main class="contenedor-ancho">
        <div class="barra-filtro">
            <span>Filtrar por: <strong>Beauty & Fragancias</strong></span>
            <span>Ordenar por: <strong>Nuestros elegidos</strong></span>
        </div>

        <div class="grilla-productos">
            <?php foreach ($productos as $prod): ?>
                <article class="card-producto">
                    <div class="card-imagen" style="background-color: <?= escapar($prod['color']) ?>;">
                        <span class="tag-producto"><?= escapar($prod['etiqueta']) ?></span>
                        <img src="<?= escapar($prod['img']) ?>" alt="<?= escapar($prod['nombre']) ?>" onerror="this.style.display='none'">
                    </div>
                    <div class="card-info">
                        <h2><?= escapar($prod['nombre']) ?></h2>
                        <p class="precio"><?= escapar($prod['precio']) ?></p>
                        <button type="button" class="btn-carrito">AGREGAR AL CARRITO</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>