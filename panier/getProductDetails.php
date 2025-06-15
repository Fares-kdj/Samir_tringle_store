<?php
header('Content-Type: application/json; charset=UTF-8');

include '../Dash/includes/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $productIds = $input['product_ids'] ?? [];

    if (empty($productIds)) {
        throw new Exception("معرفات المنتجات غير صالحة");
    }

    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $sql = "SELECT id, title, price, image_urls, color, type, diameter FROM projects WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    foreach ($productIds as $index => $id) {
        $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        throw new Exception("لم يتم العثور على المنتجات");
    }

    // Normaliser les données
    $products = array_map(function($product) {
        $product['image_urls'] = !empty($product['image_urls']) ? explode(',', $product['image_urls']) : [];
        $product['colors'] = !empty($product['color']) ? explode(',', $product['color']) : [];
        $product['types'] = !empty($product['type']) ? explode(',', $product['type']) : [];
        $product['diameters'] = !empty($product['diameter']) ? explode(',', $product['diameter']) : [];
        return $product;
    }, $products);

    echo json_encode(['success' => true, 'products' => $products]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>