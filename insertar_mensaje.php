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
    die(json_encode(["status" => "error", "message" => "Error de conexión a la base de datos: " . $conn->connect_error]));
}

// Capturar los datos del formulario
$correo = $_POST['correo'] ?? '';
$contenido_correo = $_POST['contenido_correo'] ?? '';

// Validar los datos
if (empty($correo) || empty($contenido_correo)) {
    echo json_encode(["status" => "error", "message" => "El correo y el mensaje son obligatorios."]);
    exit;
}

// Preparar la consulta SQL para insertar los datos en la tabla mensajes
$stmt = $conn->prepare("INSERT INTO mensajes (correo, contenido_correo) VALUES (?, ?)");
$stmt->bind_param("ss", $correo, $contenido_correo);

// Ejecutar la consulta y verificar si fue exitosa
if ($stmt->execute()) {
    // Respuesta exitosa
    echo json_encode(["status" => "success", "message" => "Mensaje enviado correctamente."]);
} else {
    // Respuesta de error
    echo json_encode(["status" => "error", "message" => "Error al guardar el mensaje: " . $stmt->error]);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
