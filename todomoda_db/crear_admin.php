<?php
require_once __DIR__ . '/conexion.php';

$hash = password_hash('admin123', PASSWORD_DEFAULT);

try {
    $sql = 'INSERT INTO usuarios (nombre, apellido, dni, email, password_hash, rol) 
            VALUES (:nombre, :apellido, :dni, :email, :password_hash, :rol)';
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'nombre' => 'Admin',
        'apellido' => 'Todomoda',
        'dni' => '99999999',
        'email' => 'admin@todomoda.com',
        'password_hash' => $hash,
        'rol' => 'admin'
    ]);
    echo 'Administrador creado con éxito. Correo: admin@todomoda.com | Clave: admin123. BORRÁ ESTE ARCHIVO AHORA.';
} catch (PDOException $e) {
    echo 'El administrador ya existe o hubo un error.';
}
?>
