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

    // Consultar solo los datos necesarios: total, nombre, telefono, direccion, pedido
    $query = "SELECT id_orden, nombre, telefono, direccion, pedido, total FROM ordenes";

    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $ordenes = [];
    $totalGanancias = 0;

    while ($row = $result->fetch_assoc()) {
        // Convertir el total a número para la suma
        $total = floatval($row['total']);
        $totalGanancias += $total;
        
        $ordenes[] = $row;
    }

    echo json_encode([
        'success' => true,
        'ordenes' => $ordenes,
        'total_ganancias' => $totalGanancias
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>
