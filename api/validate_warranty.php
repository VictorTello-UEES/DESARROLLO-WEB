<?php
/**
 * GREEKYA Warranty Validation API
 * Validates product warranty by series number
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../config/config.php';

// Obtener el número de serie de la solicitud
$seriesNumber = isset($_POST['series_number']) ? sanitizeInput($_POST['series_number']) : '';

if (empty($seriesNumber)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor ingrese un número de serie válido.'
    ]);
    exit;
}

try {
    // Buscar producto por número de serie
    $sql = "SELECT * FROM products WHERE series_number = :series_number LIMIT 1";
    $product = fetchOne($sql, ['series_number' => $seriesNumber]);

    if (!$product) {
        // Registrar validación fallida
        $logSql = "INSERT INTO warranty_validations (series_number, ip_address, user_agent, result) 
                   VALUES (:series_number, :ip_address, :user_agent, 'invalid')";
        executeQuery($logSql, [
            'series_number' => $seriesNumber,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);

        echo json_encode([
            'success' => false,
            'message' => 'Número de serie no encontrado en nuestra base de datos.',
            'result' => 'invalid'
        ]);
        exit;
    }

    // Verificar estado de garantía
    $warrantyStatus = 'valid';
    $warrantyMessage = 'Garantía vigente';

    if ($product['status'] === 'void') {
        $warrantyStatus = 'void';
        $warrantyMessage = 'Garantía anulada';
    } elseif ($product['status'] === 'expired') {
        $warrantyStatus = 'expired';
        $warrantyMessage = 'Garantía expirada';
    } elseif ($product['purchase_date']) {
        $expirationDate = getWarrantyExpiration($product['purchase_date'], $product['warranty_months']);
        if (!isWarrantyValid($product['purchase_date'], $product['warranty_months'])) {
            $warrantyStatus = 'expired';
            $warrantyMessage = 'Garantía expirada el ' . formatDate($expirationDate);
        } else {
            $warrantyMessage = 'Garantía válida hasta ' . formatDate($expirationDate);
        }
    }

    // Registrar validación exitosa
    $logSql = "INSERT INTO warranty_validations (series_number, ip_address, user_agent, result, product_id) 
               VALUES (:series_number, :ip_address, :user_agent, :result, :product_id)";
    executeQuery($logSql, [
        'series_number' => $seriesNumber,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'result' => $warrantyStatus,
        'product_id' => $product['id']
    ]);

    // Analizar imágenes si están almacenadas como JSON
    $images = [];
    if (!empty($product['images'])) {
        $images = json_decode($product['images'], true) ?? [];
    }

    // Devolver respuesta de éxito con detalles del producto
    echo json_encode([
        'success' => true,
        'result' => $warrantyStatus,
        'message' => $warrantyMessage,
        'product' => [
            'model_name' => $product['model_name'],
            'series_number' => $product['series_number'],
            'category' => $product['category'],
            'product_type' => $product['product_type'] ?? 'monitor',
            'size' => $product['size'],
            'type' => $product['type'],
            'resolution' => $product['resolution'],
            'refresh_rate' => $product['refresh_rate'],
            'panel_type' => $product['panel_type'],
            'warranty_months' => $product['warranty_months'],
            'purchase_date' => $product['purchase_date'] ? formatDate($product['purchase_date']) : 'No registrada',
            'images' => $images,
            'price' => formatPrice($product['price']),
            'processor' => $product['processor'] ?? 'N/A',
            'ram' => $product['ram'] ?? 'N/A',
            'storage' => $product['storage'] ?? 'N/A',
            'gpu' => $product['gpu'] ?? 'N/A',
            'has_rgb' => (bool) ($product['has_rgb'] ?? 0),
            'ports' => $product['ports'] ?? 'N/A'
        ]
    ]);

} catch (Exception $e) {
    error_log("Warranty Validation Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la validación. Por favor intente nuevamente.'
    ]);
}
?>