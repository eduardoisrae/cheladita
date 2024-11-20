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

// Función para logging
function logError($message) {
    error_log(date('Y-m-d H:i:s') . " - " . $message . "\n", 3, "debug.log");
}

// Crear la conexión
$conn = new mysqli(
    $dbConfig['DB_HOST'],
    $dbConfig['DB_USERNAME'],
    $dbConfig['DB_PASSWORD'],
    $dbConfig['DB_DATABASE'],
    $dbConfig['DB_PORT']
);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    logError("Error de conexión: " . $conn->connect_error);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// Establecer el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Obtener el contenido raw del request
$rawData = file_get_contents('php://input');
logError("Datos recibidos: " . $rawData);

// Comprobamos si se recibe JSON
$data = json_decode($rawData, true);

// Si no se recibe JSON o los datos son incorrectos, devolver un error
if ($data === null) {
    logError("Error decodificando JSON: " . json_last_error_msg());
    echo json_encode(['success' => false, 'error' => 'Datos no válidos']);
    exit;
}

// Recuperar los datos del pedido desde el JSON
$nombre = $conn->real_escape_string($data['nombre'] ?? '');
$telefono = $conn->real_escape_string($data['telefono'] ?? '');
$direccion = $conn->real_escape_string($data['direccion'] ?? '');
$total = floatval($data['total'] ?? 0); // Convertimos explícitamente a float
$pedido = $conn->real_escape_string($data['pedido'] ?? '');
$id_usuario = intval($data['id_usuario'] ?? 0); // Convertimos explícitamente a integer

// Log de los valores procesados
logError("Valores procesados:");
logError("nombre: $nombre");
logError("telefono: $telefono");
logError("direccion: $direccion");
logError("total: $total");
logError("pedido: $pedido");
logError("id_usuario: $id_usuario");

// Asegurarse de que los campos no estén vacíos
if (empty($nombre) || empty($telefono) || empty($direccion) || empty($pedido) || $total <= 0) {
    logError("Validación fallida - campos vacíos o total <= 0");
    echo json_encode([
        'success' => false, 
        'error' => 'Todos los campos son obligatorios y el total debe ser mayor que 0',
        'debug' => [
            'nombre' => empty($nombre),
            'telefono' => empty($telefono),
            'direccion' => empty($direccion),
            'pedido' => empty($pedido),
            'total' => $total
        ]
    ]);
    exit;
}

// Preparar la consulta SQL con prepared statements para mayor seguridad
$query = "INSERT INTO ordenes (id_usuario, total, nombre, telefono, direccion, pedido) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    logError("Error preparando la consulta: " . $conn->error);
    echo json_encode(['success' => false, 'error' => 'Error preparando la consulta']);
    exit;
}

// Vincular los parámetros
$stmt->bind_param("idssss", $id_usuario, $total, $nombre, $telefono, $direccion, $pedido);

// Ejecutar la consulta
if ($stmt->execute()) {
    logError("Inserción exitosa. ID: " . $stmt->insert_id);
    echo json_encode(['success' => true, 'order_id' => $stmt->insert_id]);
} else {
    logError("Error en la inserción: " . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'Error al guardar el pedido: ' . $stmt->error]);
}

// Cerrar el statement y la conexión
$stmt->close();
$conn->close();
?>