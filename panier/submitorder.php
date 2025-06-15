<?php
header('Content-Type: application/json; charset=UTF-8');

include '../Dash/includes/db.php';

try {
    // Récupérer les données du formulaire
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $customer_address = $_POST['customer_address'] ?? '';
    $products = $_POST['products'] ?? [];
    $orderDetails = json_decode($_POST['orderDetails'] ?? '[]', true);

    // Validation des champs obligatoires
    if (empty($customer_name) || empty($customer_phone) || empty($customer_address) || empty($orderDetails)) {
        throw new Exception("جميع الحقول الأساسية مطلوبة.");
    }

    // Préparer la requête SQL pour insérer les données dans la table `orders`
    $sql = "INSERT INTO orders (product_id, customer_name, customer_phone, customer_address, color, type, diameter, product_quantity, product_title, product_total, order_date, status)
            VALUES (:product_id, :customer_name, :customer_phone, :customer_address, :color, :type, :diameter, :product_quantity, :product_title, :product_total, NOW(), 'confirmed')";
    $stmt = $conn->prepare($sql);

    // Insérer chaque produit de la commande
    foreach ($orderDetails as $index => $item) {
        $productId = $item['id'];
        $productQuantity = $item['quantity'];
        $productColor = $products[$productId]['color'] ?? '';
        $productType = $products[$productId]['type'] ?? '';
        $productDiameter = $products[$productId]['diameter'] ?? '';
        $productTitle = $item['title'];
        $productTotal = $item['total'];

        // Lier les paramètres
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':customer_name', $customer_name, PDO::PARAM_STR);
        $stmt->bindParam(':customer_phone', $customer_phone, PDO::PARAM_STR);
        $stmt->bindParam(':customer_address', $customer_address, PDO::PARAM_STR);
        $stmt->bindParam(':color', $productColor, PDO::PARAM_STR);
        $stmt->bindParam(':type', $productType, PDO::PARAM_STR);
        $stmt->bindParam(':diameter', $productDiameter, PDO::PARAM_STR);
        $stmt->bindParam(':product_quantity', $productQuantity, PDO::PARAM_INT);
        $stmt->bindParam(':product_title', $productTitle, PDO::PARAM_STR);
        $stmt->bindParam(':product_total', $productTotal, PDO::PARAM_STR);

        // Exécuter la requête
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'insertion de la commande pour le produit ID $productId.");
        }
    }

    // Réponse JSON en cas de succès
    echo json_encode(['success' => true, 'message' => 'تم تسجيل طلبك بنجاح! سنتواصل معك قريبًا.']);
} catch (Exception $e) {
    // En cas d'erreur, renvoyer un message d'erreur
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn = null;
}
?>