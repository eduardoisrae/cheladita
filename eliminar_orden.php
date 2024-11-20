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

    // Obtener el ID de la orden a eliminar
    if (isset($_GET['id_orden'])) {
        $id_orden = intval($_GET['id_orden']);
        
        // Eliminar la orden
        $query = "DELETE FROM ordenes WHERE id_orden = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_orden);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Error al eliminar la orden: " . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception("No se proporcionó un ID válido.");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
