<?php
// Configuración de la base de datos
$dbConfig = [
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '108.179.194.27',
    'DB_PORT' => 3306,
    'DB_DATABASE' => 'victorlm_mir',
    'DB_USERNAME' => 'victorlm_eduardo',
    'DB_PASSWORD' => 'mistesoros0521',
];

// Crear conexión
$conn = new mysqli(
    $dbConfig['DB_HOST'],
    $dbConfig['DB_USERNAME'],
    $dbConfig['DB_PASSWORD'],
    $dbConfig['DB_DATABASE'],
    $dbConfig['DB_PORT']
);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar el formulario al recibir datos por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validar campos vacíos
    if (empty($nombre) || empty($password)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Validar si es un usuario especial
    $usuarios_especiales = ['oscar', 'ale', 'eduardo'];
    if (in_array(strtolower($nombre), $usuarios_especiales) && $password === 'enchiladamir') {
        echo "manager";
        exit;
    }

    // Verificar usuario en la base de datos
    $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Usuario encontrado, verificar contraseña
        $stmt->bind_result($password_hash);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            echo "sesion";
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "El usuario no existe.";
    }

    $stmt->close();
}
$conn->close();
?>
