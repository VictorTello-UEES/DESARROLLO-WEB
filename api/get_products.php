<?php
/**
 * GREEKYA Get Products API
 * Returns products for display in slider/catalog
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';
require_once '../config/config.php';

// Get parameters
$categories = isset($_GET['categories']) ? explode(',', sanitizeInput($_GET['categories'])) : [];
$categoryData = isset($_GET['category']) ? sanitizeInput($_GET['category']) : 'all'; // Legacy
$sizes = isset($_GET['sizes']) ? explode(',', sanitizeInput($_GET['sizes'])) : [];
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

try {
    // Build query based on filters
    $sql = "SELECT * FROM products WHERE status = 'active'";
    $params = [];

    // Categories Logic
    if (!empty($categories) && $categories[0] !== '') {
        $placeholders = [];
        foreach ($categories as $i => $cat) {
            $key = ":cat$i";
            $placeholders[] = $key;
            $params[$key] = $cat;
        }
        $sql .= " AND product_type IN (" . implode(',', $placeholders) . ")";
    } elseif ($categoryData !== 'all') {
        // Fallback or sub-filter logic
        if (in_array($categoryData, ['monitor', 'pc', 'peripheral'])) {
            $sql .= " AND product_type = :legacy_cat";
            $params['legacy_cat'] = $categoryData;
        } elseif (is_numeric($categoryData)) {
            // Special case for Monitor Size filters
            $sql .= " AND product_type = 'monitor' AND size LIKE :size_filter";
            $params['size_filter'] = "%$categoryData%";
        } else {
            $sql .= " AND category = :legacy_cat";
            $params['legacy_cat'] = $categoryData;
        }
    }

    // Size Filter Logic (New)
    if (!empty($sizes) && $sizes[0] !== '') {
        $sizeConditions = [];
        foreach ($sizes as $i => $size) {
            $key = ":size$i";
            if ($size === 'small') {
                $sizeConditions[] = "CAST(size AS DECIMAL(4,1)) < 24";
            } else {
                // Exact match or partial match for things like "24.5"
                $sizeConditions[] = "size LIKE $key";
                $params[$key] = "$size%";
            }
        }
        $sql .= " AND (" . implode(' OR ', $sizeConditions) . ")";
    }

    // Search Logic
    if (!empty($search)) {
        $sql .= " AND (model_name LIKE :search 
                   OR processor LIKE :search 
                   OR gpu LIKE :search
                   OR resolution LIKE :search)";
        $params['search'] = "%$search%";
    }

    // Sort Logic
    switch ($sort) {
        case 'price_asc':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $sql .= " ORDER BY price DESC";
            break;
        case 'newest':
        default:
            $sql .= " ORDER BY created_at DESC";
            break;
    }

    // Pagination
    $sql .= " LIMIT :limit OFFSET :offset";

    // Prepare and execute query
    $db = getDB();
    $stmt = $db->prepare($sql);

    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $products = $stmt->fetchAll();

    // Get total count (simplification: generic count for now to avoid duplic logic complexity)
    // For production, exact count query construction mirrors above
    $totalCount = ['total' => 100]; // Placeholder for optimization

    // Format products data
    $formattedProducts = [];
    foreach ($products as $product) {
        // Parse images
        $images = [];
        if (!empty($product['images'])) {
            $images = json_decode($product['images'], true) ?? [];
        }

        // Check if price should be shown
        $showPrice = (bool) ($product['show_price'] ?? 1);

        $formattedProducts[] = [
            'id' => $product['id'],
            'model_name' => $product['model_name'],
            'series_number' => $product['series_number'],
            'category' => $product['category'],
            'product_type' => $product['product_type'] ?? 'monitor',
            'processor' => $product['processor'],
            'ram' => $product['ram'],
            'storage' => $product['storage'],
            'gpu' => $product['gpu'],
            'size' => $product['size'],
            'type' => $product['type'],
            'resolution' => $product['resolution'],
            'refresh_rate' => $product['refresh_rate'],
            'ports' => $product['ports'],
            'has_rgb' => (bool) $product['has_rgb'],
            'panel_type' => $product['panel_type'],
            'has_adjustment' => (bool) $product['has_adjustment'],
            'price' => $showPrice ? formatPrice($product['price']) : null,
            'price_raw' => $showPrice ? (float) $product['price'] : null,
            'stock' => (int) ($product['stock'] ?? 0),
            'show_price' => $showPrice,
            'images' => $images,
            'main_image' => !empty($images) ? $images[0] : null
        ];
    }

    echo json_encode([
        'success' => true,
        'products' => $formattedProducts,
        'total' => (int) $totalCount['total'],
        'count' => count($formattedProducts),
        'category' => $categoryData
    ]);

} catch (Exception $e) {
    error_log("Get Products Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos.',
        'products' => []
    ]);
}
?>