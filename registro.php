<?php
// Configuración de la base de datos
$dbConfig = [
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '108.179.194.27', // Cambia por la IP de tu base de datos
    'DB_PORT' => 3306,            // Puerto por defecto para MySQL
    'DB_DATABASE' => 'victorlm_mir', // Cambia por el nombre de tu base de datos
    'DB_USERNAME' => 'victorlm_eduardo',  // Cambia por tu usuario
    'DB_PASSWORD' => 'mistesoros0521',     // Cambia por tu contraseña
];

// Crear conexión usando mysqli
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
} else {
    echo "Conexión exitosa a la base de datos.";
}

// Aquí va el resto del código para procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $password1 = $_POST['password1'] ?? '';

    // Validar campos vacíos
    if (empty($nombre) || empty($password1)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE nombre = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "El nombre de usuario ya existe.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Encriptar la contraseña
    $password_hash = password_hash($password1, PASSWORD_DEFAULT);

    // Insertar usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, contrasena) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $password_hash);

    if ($stmt->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error al registrar el usuario. Intenta nuevamente.";
    }
    $stmt->close();
}

$conn->close();
?>
