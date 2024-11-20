<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$dbConfig = [
    'DB_HOST' => '108.179.194.27',
    'DB_PORT' => 3306,
    'DB_DATABASE' => 'victorlm_mir',
    'DB_USERNAME' => 'victorlm_eduardo',
    'DB_PASSWORD' => 'mistesoros0521'
];

try {
    // Crear la conexión
    $conn = new mysqli(
        $dbConfig['DB_HOST'],
        $dbConfig['DB_USERNAME'],
        $dbConfig['DB_PASSWORD'],
        $dbConfig['DB_DATABASE'],
        $dbConfig['DB_PORT']
    );

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    // Obtener los mensajes
    $query = "SELECT * FROM mensajes";  // Selecciona todos los mensajes
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $mensajes = [];
        while ($row = $result->fetch_assoc()) {
            $mensajes[] = [
                'id_mensaje' => $row['id_mensaje'],
                'correo' => $row['correo'],
                'contenido_correo' => $row['contenido_correo']
            ];
        }

        // Enviar los mensajes como respuesta JSON
        echo json_encode(['success' => true, 'mensajes' => $mensajes]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se encontraron mensajes']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
