<?php
session_start();
include './Dash/includes/db.php';

// Vérifier si l'ID du produit est fourni
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    error_log("Erreur : product_id manquant ou invalide");
    header('Location: index.php');
    exit;
}

$product_id = $_GET['product_id'];

// Tester la connexion à la base de données
try {
    $conn->query("SELECT 1");
    error_log("Connexion à la base de données réussie");
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    header('Location: index.php');
    exit;
}

// Récupérer les détails du produit
try {
    $sql = "SELECT * FROM projects WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        error_log("Erreur : Produit non trouvé pour product_id=$product_id");
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération du produit : " . $e->getMessage());
    header('Location: index.php');
    exit;
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    error_log("Données POST reçues : " . print_r($_POST, true));

    $customer_name = $_POST['customer_name'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $customer_address = $_POST['customer_address'] ?? '';
    $product_quantity = $_POST['product_quantity'] ?? 1;
    $product_color = $_POST['product_color'] ?? '';
    $product_type = $_POST['product_type'] ?? '';
    $product_diameter = $_POST['product_diameter'] ?? '';

    // Validation des champs obligatoires
    if (empty($customer_name) || empty($customer_phone) || empty($customer_address) || $product_quantity < 1) {
        $response = ['success' => false, 'message' => "جميع الحقول الأساسية مطلوبة، ويجب أن تكون الكمية أكبر من 0."];
    } else {
        // Vérifier les caractéristiques seulement si elles existent
        $colors = !empty($product['color']) ? explode(',', $product['color']) : [];
        $types = !empty($product['type']) ? explode(',', $product['type']) : [];
        $diameters = !empty($product['diameter']) ? explode(',', $product['diameter']) : [];

        $missing_fields = [];
        if (!empty($colors) && empty($product_color)) {
            $missing_fields[] = "اللون";
        }
        if (!empty($types) && empty($product_type)) {
            $missing_fields[] = "النوع";
        }
        if (!empty($diameters) && empty($product_diameter)) {
            $missing_fields[] = "القطر";
        }

        if (!empty($missing_fields)) {
            $response = ['success' => false, 'message' => "يرجى اختيار " . implode('، ', $missing_fields) . "."];
        } else {
            // Insérer la commande dans la table orders
            try {
                $sql = "INSERT INTO orders (product_id, customer_name, customer_phone, customer_address, color, type, diameter, product_quantity, order_date, status) 
                        VALUES (:product_id, :customer_name, :customer_phone, :customer_address, :color, :type, :diameter, :product_quantity, NOW(), 'confirmed')";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                $stmt->bindParam(':customer_name', $customer_name, PDO::PARAM_STR);
                $stmt->bindParam(':customer_phone', $customer_phone, PDO::PARAM_STR);
                $stmt->bindParam(':customer_address', $customer_address, PDO::PARAM_STR);
                $stmt->bindParam(':color', $product_color, PDO::PARAM_STR);
                $stmt->bindParam(':type', $product_type, PDO::PARAM_STR);
                $stmt->bindParam(':diameter', $product_diameter, PDO::PARAM_STR);
                $stmt->bindParam(':product_quantity', $product_quantity, PDO::PARAM_INT);

                error_log("Requête SQL : " . $sql);
                error_log("Valeurs bindées : product_id=$product_id, customer_name=$customer_name, customer_phone=$customer_phone, customer_address=$customer_address, color=$product_color, type=$product_type, diameter=$product_diameter, product_quantity=$product_quantity");

                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => "تم تسجيل طلبك بنجاح! سنتواصل معك قريبًا."];
                } else {
                    error_log("Erreur : Échec de l'exécution de la requête SQL");
                    $response = ['success' => false, 'message' => "فشل في تسجيل الطلب. حاول مرة أخرى."];
                }
            } catch (PDOException $e) {
                error_log("Erreur SQL lors de l'insertion de la commande : " . $e->getMessage());
                $response = ['success' => false, 'message' => "خطأ في قاعدة البيانات: " . $e->getMessage()];
            }
        }
    }

    // Renvoyer une réponse JSON pour les requêtes AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response);
        exit;
    }

    // Pour les soumissions classiques, stocker le message pour l'affichage
    $message = $response['message'];
}

// Préparer les données du produit
$image_urls = !empty($product['image_urls']) ? explode(',', $product['image_urls']) : [];
$normalized_image_urls = array_map(function($url) {
    return './Dash/uploads/' . basename($url);
}, $image_urls);
$colors = !empty($product['color']) ? explode(',', $product['color']) : [];
$types = !empty($product['type']) ? explode(',', $product['type']) : [];
$diameters = !empty($product['diameter']) ? explode(',', $product['diameter']) : [];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب المنتج - <?php echo htmlspecialchars($product['title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/images/Logo3.png"/>
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/style5.css">
    <style>
        .order-container {
            max-width: 1300px; /* Largeur pour desktop */
            margin: 180px auto 20px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-details {
            flex: 1;
            min-width: 300px;
        }
        .order-form {
            flex: 1;
            min-width: 300px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product-images {
            position: relative;
            margin: 20px 0;
        }
        .main-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 5px;
        }
        /* Conteneur du carrousel */
        .carousel {
            position: relative;
            margin-top: 10px;
        }
        /* Conteneur des miniatures */
        .thumbnails {
            display: flex;
            flex-wrap: nowrap; /* Pas de retour à la ligne sur desktop */
            justify-content: center; /* Centrer les miniatures */
            gap: 8px; /* Espacement entre les miniatures */
            overflow: visible; /* Toutes les miniatures visibles */
            scroll-behavior: smooth;
        }
        /* Style des miniatures */
        .thumbnail {
            width: 50px; /* Taille pour desktop */
            height: 50px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 5px;
            transition: border 0.3s ease, transform 0.2s ease;
        }
        .thumbnail:hover {
            border: 2px solid #FFD700;
            transform: scale(1.05); /* Légère mise à l'échelle au survol */
        }
        /* Points de navigation */
        .carousel-dots {
            display: flex;
            justify-content: center;
            margin-top: 10px;
            gap: 8px;
        }
        .dot {
            width: 10px;
            height: 10px;
            background-color: #ddd;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .dot.active {
            background-color: #FFD700; /* Couleur pour le point actif */
        }
        .dot:hover {
            background-color: #DAA520;
        }
        /* Masquer la barre de défilement */
        .thumbnails::-webkit-scrollbar {
            display: none; /* Masquer la barre de défilement */
        }
        .thumbnails {
            -ms-overflow-style: none; /* IE et Edge */
            scrollbar-width: none; /* Firefox */
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }
        .order-btn {
            background-color: #222;
            color: #FFD700;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            border: none;
            font-family: "cairo", sans-serif;
            font-weight: bold;
        }
        .order-btn:hover {
            background-image: linear-gradient(90deg, rgba(255, 215, 0, 1) 0%, rgba(255, 193, 7, 1) 30%, rgba(218, 165, 32, 1) 60%, rgba(242, 207, 119, 1) 100%);
            color: #222;
        }
        .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 1003;
            max-width: 300px;
            height: 100px;
            font-family: "cairo", sans-serif;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .notification.show {
            opacity: 1;
        }
        .success-notification {
            background: #28a745;
            color: #fff;
        }
        .error-notification {
            background: #dc3545;
            color: #fff;
        }
        @media (max-width: 768px) {
            .main-image {
                height: 200px;
            }
            .thumbnails {
                flex-wrap: wrap; /* Activer le retour à la ligne */
                justify-content: center; /* Centrer les miniatures */
                gap: 8px;
                max-width: 308px; /* 7 miniatures de 40px + 6 espaces de 8px = 280px + 28px de marge */
                margin: 0 auto; /* Centrer le conteneur */
            }
            .thumbnail {
                width: 40px; /* Taille pour mobile */
                height: 40px;
                flex: 0 0 40px; /* Fixer la taille pour éviter l'étirement */
            }
            .thumbnail:nth-child(-n+7) {
                /* Les 7 premières miniatures sur la première ligne */
            }
            .thumbnail:nth-child(n+8) {
                /* Les 3 dernières miniatures sur la deuxième ligne */
            }
            .dot {
                width: 8px;
                height: 8px;
            }
            .notification {
                top: 80px;
                right: 10px;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar (réutilisée depuis index.php) -->
    <nav class="custom-navbar">
        <div class="nav-container">
            <a class="brand" href="./index.php">
                <img src="./assets/images/Logo.png" alt="Logo سمير ترانقل" class="brand-logo">
            </a>
            <form class="search-form">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
                <input type="text" id="searchBar" placeholder="ابحث عن منتج..." class="search-input" />
            </form>
            <div class="nav-icons">
                <div class="cart" onclick="openCart(); event.stopPropagation();">
                    <i class="fas fa-shopping-cart cart-icon"></i>
                    <span id="cart-count">0</span>
                </div>
                <div class="cart admin" onclick="window.location.href='./Dash/admin/login.php'; event.stopPropagation();">
                    <i class="fas fa-user-cog"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="order-container">
        <!-- Détails du produit -->
        <div class="product-details">
            <h2><?php echo htmlspecialchars($product['title']); ?></h2>
            <div class="product-images">
                <?php if (!empty($normalized_image_urls)): ?>
                    <!-- Image principale -->
                    <div class="main-image">
                        <img id="mainImage" src="<?php echo htmlspecialchars($normalized_image_urls[0]); ?>" alt="Product Image" style="width: 100%; height: 300px; object-fit: cover; border-radius: 5px;">
                    </div>
                    <!-- Carrousel des miniatures -->
                    <div class="carousel">
                        <div class="thumbnails">
                            <?php foreach ($normalized_image_urls as $index => $url): ?>
                                <img src="<?php echo htmlspecialchars($url); ?>" alt="Thumbnail" class="thumbnail" onclick="changeMainImage(<?php echo $index; ?>)" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer; border: <?php echo $index === 0 ? '2px solid #FFD700' : '1px solid #ddd'; ?>;">
                            <?php endforeach; ?>
                        </div>
                        <!-- Points de navigation -->
                        <div class="carousel-dots">
                            <?php foreach ($normalized_image_urls as $index => $url): ?>
                                <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeMainImage(<?php echo $index; ?>)"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="main-image">
                        <img id="mainImage" src="./assets/default-image.png" alt="No Image" style="width: 100%; height: 300px; object-fit: cover; border-radius: 5px;">
                    </div>
                    <div class="thumbnails">
                        <img src="./assets/default-image.png" alt="Thumbnail" class="thumbnail" style="width: 50px; height: 50px; object-fit: cover; margin: 5px;">
                    </div>
                <?php endif; ?>
            </div>
            <p><strong>السعر:</strong> <?php echo number_format($product['price'], 2); ?> دج</p>
            <p><strong>الفئة:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
            <p><strong>الوصف:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
            <?php if (!empty($colors)): ?>
                <p><strong>الألوان المتوفرة:</strong> <?php echo htmlspecialchars(implode(', ', $colors)); ?></p>
            <?php endif; ?>
            <?php if (!empty($types)): ?>
                <p><strong>الأنواع المتوفرة:</strong> <?php echo htmlspecialchars(implode(', ', $types)); ?></p>
            <?php endif; ?>
            <?php if (!empty($diameters)): ?>
                <p><strong>الأقطار المتوفرة:</strong> <?php echo htmlspecialchars(implode(', ', $diameters)); ?></p>
            <?php endif; ?>
        </div>

        <!-- Formulaire de commande -->
        <div class="order-form">
            <h3>نموذج الطلب</h3>
            <form id="orderForm" class="add-order-form">
                <input type="hidden" name="place_order" value="1">
                <div class="form-group">
                    <label for="customer_name">الاسم الكامل:</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="customer_phone">رقم الهاتف:</label>
                    <input type="tel" id="customer_phone" name="customer_phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="customer_address">العنوان:</label>
                    <input type="text" id="customer_address" name="customer_address" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="product_quantity">الكمية:</label>
                    <input type="number" id="product_quantity" name="product_quantity" class="form-control" min="1" value="1" required>
                </div>
                <?php if (!empty($colors)): ?>
                    <div class="form-group">
                        <label for="product_color">اللون:</label>
                        <select id="product_color" name="product_color" class="form-control" required>
                            <option value="">اختر اللون</option>
                            <?php foreach ($colors as $color): ?>
                                <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <?php if (!empty($types)): ?>
                    <div class="form-group">
                        <label for="product_type">النوع:</label>
                        <select id="product_type" name="product_type" class="form-control" required>
                            <option value="">اختر النوع</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <?php if (!empty($diameters)): ?>
                    <div class="form-group">
                        <label for="product_diameter">القطر:</label>
                        <select id="product_diameter" name="product_diameter" class="form-control" required>
                            <option value="">اختر القطر</option>
                            <?php foreach ($diameters as $diameter): ?>
                                <option value="<?php echo htmlspecialchars($diameter); ?>"><?php echo htmlspecialchars($diameter); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <button type="submit" class="order-btn">تأكيد الطلب</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour changer l'image principale en fonction de la miniature ou du point cliqué
        const images = <?php echo json_encode($normalized_image_urls); ?>;
        let currentImageIndex = 0;

        function changeMainImage(index) {
            if (images.length === 0) return;
            currentImageIndex = index;
            document.getElementById('mainImage').src = images[currentImageIndex];

            // Mettre à jour les bordures des miniatures
            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach((thumb, i) => {
                thumb.style.border = i === currentImageIndex ? '2px solid #FFD700' : '1px solid #ddd';
            });

            // Mettre à jour les points de navigation
            const dots = document.querySelectorAll('.dot');
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentImageIndex);
            });
        }

        // (Optionnel) Défilement automatique du carrousel
        function autoSlide() {
            if (images.length === 0) return;
            currentImageIndex = (currentImageIndex + 1) % images.length;
            changeMainImage(currentImageIndex);
        }

        // Activer le défilement automatique toutes les 5 secondes (facultatif)
        setInterval(autoSlide, 5000);

        // Fonction pour afficher une notification
        function showNotification(message, isSuccess) {
            const notification = document.createElement('div');
            notification.className = `notification ${isSuccess ? 'success-notification' : 'error-notification'}`;
            notification.textContent = message;
            document.querySelector('.order-container').prepend(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Gestion de la soumission du formulaire via AJAX
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Statut HTTP:', response.status);
                console.log('Réponse brute:', response);
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('Texte brut de la réponse:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Données JSON:', data);
                    showNotification(data.message, data.success);
                    if (data.success) {
                        document.getElementById('orderForm').reset();
                    }
                } catch (error) {
                    console.error('Erreur de parsing JSON:', error);
                    throw new Error('Réponse non-JSON: ' + text);
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                showNotification('❌ حدث خطأ أثناء إرسال الطلب: ' + error.message, false);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const notification = document.querySelector('.success-notification, .error-notification');
            if (notification) {
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        notification.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>
</html>