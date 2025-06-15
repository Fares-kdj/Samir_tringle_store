<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php';

// Endpoint AJAX pour récupérer les données d'un produit
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_product') {
    $product_id = $_GET['id'] ?? 0;
    $sql = "SELECT id, title, description, category, price, image_urls, color, type, diameter FROM projects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'المنتج غير موجود.']);
    }
    exit;
}

// Endpoint AJAX pour mettre à jour un produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? '';
    $color = $_POST['color'] ?? null;
    $type = $_POST['type'] ?? null;
    $diameter = $_POST['diameter'] ?? null;
    $files = $_FILES['file'] ?? null;

    $response = ['success' => false, 'message' => ''];

    if (empty($title) || empty($description) || empty($category) || empty($price)) {
        $response['message'] = "جميع الحقول الأساسية مطلوبة.";
    } else {
        $sql = "SELECT image_urls FROM projects WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $image_urls = $product['image_urls'] ? explode(',', $product['image_urls']) : [];

        $target_dir = "../uploads";
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/mov'];
        $max_file_size = 20 * 1024 * 1024; // 20MB

        if ($files && $files['name'][0] !== '') {
            if (count($files['name']) > 10) {
                $response['message'] = "يمكنك رفع 10 ملفات كحد أقصى.";
            } else {
                $image_urls = []; // Réinitialiser si de nouveaux fichiers sont uploadés
                for ($i = 0; $i < count($files['name']); $i++) {
                    $file = [
                        'name' => $files['name'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'type' => $files['type'][$i],
                        'size' => $files['size'][$i]
                    ];

                    if ($file['size'] > $max_file_size) {
                        $response['message'] = "الملف " . $file['name'] . " يتجاوز الحجم المسموح به.";
                        break;
                    }

                    $file_type = mime_content_type($file['tmp_name']);
                    $target_file = $target_dir . basename($file['name']);

                    if (in_array($file_type, $allowed_types)) {
                        if (in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
                            $new_width = 3700;
                            $new_height = 1875;

                            switch ($file_type) {
                                case 'image/jpeg':
                                    $source = @imagecreatefromjpeg($file['tmp_name']);
                                    break;
                                case 'image/png':
                                    $source = @imagecreatefrompng($file['tmp_name']);
                                    break;
                                case 'image/gif':
                                    $source = @imagecreatefromgif($file['tmp_name']);
                                    break;
                                default:
                                    $source = false;
                            }

                            if ($source === false) {
                                $response['message'] = "فشل تحميل الصورة: " . $file['name'];
                                break;
                            }

                            $orig_width = imagesx($source);
                            $orig_height = imagesy($source);

                            $canvas = imagecreatetruecolor($new_width, $new_height);
                            $white = imagecolorallocate($canvas, 255, 255, 255);
                            imagefill($canvas, 0, 0, $white);

                            $ratio = min($new_width / $orig_width, $new_height / $orig_height);
                            $scaled_width = $orig_width * $ratio;
                            $scaled_height = $orig_height * $ratio;

                            $x = ($new_width - $scaled_width) / 2;
                            $y = ($new_height - $scaled_height) / 2;

                            imagecopyresampled($canvas, $source, $x, $y, 0, 0, $scaled_width, $scaled_height, $orig_width, $orig_height);

                            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $unique_filename = uniqid('img_') . '.' . $file_extension;
                            $target_file = $target_dir . $unique_filename;

                            switch ($file_type) {
                                case 'image/jpeg':
                                    imagejpeg($canvas, $target_file, 90);
                                    break;
                                case 'image/png':
                                    imagepng($canvas, $target_file, 9);
                                    break;
                                case 'image/gif':
                                    imagegif($canvas, $target_file);
                                    break;
                            }

                            imagedestroy($source);
                            imagedestroy($canvas);

                            $image_urls[] = $target_file;
                        } else {
                            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                                $image_urls[] = $target_file;
                            } else {
                                $response['message'] = "حدث خطأ أثناء رفع الملف " . $file['name'];
                                break;
                            }
                        }
                    } else {
                        $response['message'] = "نوع الملف غير مسموح به: " . $file['name'];
                        break;
                    }
                }
            }
        }

        if (empty($response['message'])) {
            $image_urls_str = implode(',', $image_urls);
            $sql = "UPDATE projects SET title = ?, description = ?, image_urls = ?, category = ?, price = ?, color = ?, type = ?, diameter = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$title, $description, $image_urls_str, $category, $price, $color, $type, $diameter, $product_id]);
            $response['success'] = true;
            $response['message'] = "تم تحديث المنتج بنجاح: " . $title;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? '';
    $files = $_FILES['file'] ?? null;
    $color = $_POST['color'] ?? null;
    $type = $_POST['type'] ?? null;
    $diameter = $_POST['diameter'] ?? null;

    if (empty($title) || empty($description) || empty($category) || empty($price) || empty($files)) {
        $_SESSION['message'] = "جميع الحقول مطلوبة.";
    } else {
        $target_dir = "../uploads";
        $image_urls = [];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/mov'];
        $max_file_size = 20 * 1024 * 1024; // 20MB

        if (count($files['name']) > 10) {
            $_SESSION['message'] = "يمكنك رفع 10 ملفات كحد أقصى.";
        } else {
            for ($i = 0; $i < count($files['name']); $i++) {
                $file = [
                    'name' => $files['name'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'type' => $files['type'][$i],
                    'size' => $files['size'][$i]
                ];

                if ($file['size'] > $max_file_size) {
                    $_SESSION['message'] = "الملف " . $file['name'] . " يتجاوز الحجم المسموح به.";
                    break;
                }

                $file_type = mime_content_type($file['tmp_name']);
                $target_file = $target_dir . basename($file['name']);

                if (in_array($file_type, $allowed_types)) {
                    if (in_array($file_type, ['image/jpeg', 'image/png', 'image/gif'])) {
                        $new_width = 3700;
                        $new_height = 1875;

                        switch ($file_type) {
                            case 'image/jpeg':
                                $source = @imagecreatefromjpeg($file['tmp_name']);
                                break;
                            case 'image/png':
                                $source = @imagecreatefrompng($file['tmp_name']);
                                break;
                            case 'image/gif':
                                $source = @imagecreatefromgif($file['tmp_name']);
                                break;
                            default:
                                $source = false;
                        }

                        if ($source === false) {
                            $_SESSION['message'] = "فشل تحميل الصورة: " . $file['name'];
                            break;
                        }

                        $orig_width = imagesx($source);
                        $orig_height = imagesy($source);

                        $canvas = imagecreatetruecolor($new_width, $new_height);
                        $white = imagecolorallocate($canvas, 255, 255, 255);
                        imagefill($canvas, 0, 0, $white);

                        $ratio = min($new_width / $orig_width, $new_height / $orig_height);
                        $scaled_width = $orig_width * $ratio;
                        $scaled_height = $orig_height * $ratio;

                        $x = ($new_width - $scaled_width) / 2;
                        $y = ($new_height - $scaled_height) / 2;

                        imagecopyresampled($canvas, $source, $x, $y, 0, 0, $scaled_width, $scaled_height, $orig_width, $orig_height);

                        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $unique_filename = uniqid('img_') . '.' . $file_extension;
                        $target_file = $target_dir . $unique_filename;

                        switch ($file_type) {
                            case 'image/jpeg':
                                imagejpeg($canvas, $target_file, 90);
                                break;
                            case 'image/png':
                                imagepng($canvas, $target_file, 9);
                                break;
                            case 'image/gif':
                                imagegif($canvas, $target_file);
                                break;
                        }

                        imagedestroy($source);
                        imagedestroy($canvas);

                        $image_urls[] = $target_file;
                    } else {
                        if (move_uploaded_file($file['tmp_name'], $target_file)) {
                            $image_urls[] = $target_file;
                        } else {
                            $_SESSION['message'] = "حدث خطأ أثناء رفع الملف " . $file['name'];
                            break;
                        }
                    }
                } else {
                    $_SESSION['message'] = "نوع الملف غير مسموح به: " . $file['name'];
                    break;
                }
            }
        }

        if (empty($_SESSION['message'])) {
            $image_urls_str = implode(',', $image_urls);
            $sql = "INSERT INTO projects (title, description, image_urls, category, price, color, type, diameter) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$title, $description, $image_urls_str, $category, $price, $color, $type, $diameter]);
            $_SESSION['message'] = "تمت إضافة المنتج بنجاح: " . $title;
        }

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Fetch image_urls before deletion
    $sql = "SELECT image_urls FROM projects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$delete_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete associated image files
    if ($product && !empty($product['image_urls'])) {
        $image_urls = explode(',', $product['image_urls']);
        foreach ($image_urls as $image_url) {
            if (file_exists($image_url)) {
                unlink($image_url); // Delete the file from the filesystem
            }
        }
    }

    // Delete the product from the database
    $sql = "DELETE FROM projects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$delete_id])) {
        $_SESSION['message'] = "تم حذف المنتج وملفاته بنجاح!";
    } else {
        $_SESSION['message'] = "❌ حدث خطأ أثناء حذف المنتج";
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle order deletion
if (isset($_GET['delete_order_id'])) {
    $delete_order_id = $_GET['delete_order_id'];
    $sql = "DELETE FROM orders WHERE id = :delete_order_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':delete_order_id', $delete_order_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $_SESSION['message'] = "تم حذف الطلب بنجاح!";
    } else {
        $_SESSION['message'] = "❌ حدث خطأ أثناء حذف الطلب";
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle order status update
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $sql = "UPDATE orders SET status = :status WHERE id = :order_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $_SESSION['message'] = "تم تحديث حالة الطلب بنجاح!";
    } else {
        $_SESSION['message'] = "❌ حدث خطأ أثناء تحديث حالة الطلب";
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch products
$sql = "SELECT id, title, description, category, price, image_urls, color, type, diameter FROM projects ORDER BY id DESC";
$stmt = $conn->query($sql);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
error_log("Données des produits récupérées : " . print_r($projects, true));

// Fetch orders with total price calculation, status, and image_urls
$sql = "SELECT orders.*, projects.title, projects.price, projects.image_urls, orders.product_size, orders.product_quantity, 
               (orders.product_quantity * projects.price) AS total_price 
        FROM orders 
        JOIN projects ON orders.product_id = projects.id 
        ORDER BY order_date DESC";
$stmt = $conn->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Récupérer le message de la session et le supprimer après affichage
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="icon" type="image/png" href="../../assets/images/logo3.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./board7.8.css">
</head>
<body>
    <div class="app-container">

<!-- Barre latérale -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="app-icon">
        <div class="logo-image"></div>
        </div>
       
    </div>
    <ul class="sidebar-list">
        <li class="sidebar-list-item active" data-view="products">
            <a href="#" onclick="event.preventDefault();">
                <i class="fas fa-cube"></i>
                <span>المنتجات</span>
            </a>
        </li>
        <li class="sidebar-list-item" data-view="orders">
            <a href="#" onclick="event.preventDefault();">
                <i class="fas fa-box"></i>
                <span>إدارة الطلبات</span>
            </a>
        </li>
    </ul>
    
 
</div>

        <!-- Contenu principal -->
        <div class="app-content">
            <div class="app-content-header">
                <h1 class="app-content-headerText" id="header-text">المنتجات</h1>
                <button class="mode-switch" title="تبديل الثيم">
                    <i class="fas fa-moon"></i>
                     <!-- Hamburger Icon for Mobile -->
     <button class="hamburger" aria-label="Toggle mobile menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
       <!-- Mobile Menu Panel -->
   <div class="mobile-menu" id="mobileMenu">
        <button class="close-menu" aria-label="Close mobile menu">×</button>
        <div class="mobile-brand">
            <img src="../../assets/Folia-white.png" alt="شعار فوليا" style="width: 24px; height: 24px;">
            <span>فوليا</span>
        </div>
        <ul class="mobile-nav-links">
            <!-- Sidebar items will be injected dynamically via JS -->
        </ul>
    </div>
                </button>
            </div>
            <div class="app-content-actions">
                <input class="search-bar" placeholder="بحث..." type="text">
                <div class="app-content-actions-wrapper">
                    <div class="filter-button-wrapper">
                        <button class="action-button filter jsFilter">
                            <span>تصفية</span>
                            <i class="fas fa-filter"></i>
                        </button>
                        <div class="filter-menu">
                            <label>الفئة</label>
                            <select id="filter-category">
                                <!-- Les options seront remplies dynamiquement par JavaScript -->
                            </select>
                            <div class="filter-menu-buttons">
                                <button class="filter-button reset">إعادة تعيين</button>
                                <button class="filter-button apply">تطبيق</button>
                            </div>
                        </div>
                    </div>
                    <button class="action-button list active" title="عرض القائمة">
                        <i class="fas fa-list"></i>
                    </button>
                    <button class="action-button grid" title="عرض الشبكة">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="action-button plus" onclick="openAddProductModal()">
                        <span>إضافة منتج</span>
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <?php if (!empty($message)): ?>
                <div class="<?php echo strpos($message, 'خطأ') !== false ? 'error-notification' : 'success-notification'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <div class="products-area-wrapper tableView" id="content-area">
                <!-- Le contenu sera généré dynamiquement par JavaScript -->
            </div>
        </div>
    </div>

    <!-- Panneau d'ajout de produit (Modal) -->
    <div class="modal" id="addProductModal">
        <div class="modal-content">
            <h2>إضافة منتج جديد</h2>
            <form method="POST" enctype="multipart/form-data" class="add-project-form">
                <input type="hidden" name="add_product" value="1">
                <div class="form-group">
                    <label for="title">العنوان:</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">الوصف:</label>
                    <textarea id="description" name="description" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="category">الفئة:</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="ترانقل">ترانقل</option>
                        <option value="موتيف">موتيف</option>
                        <option value="سيبور">سيبور</option>
                        <option value="بوشون">بوشون</option>
                        <option value="انو">انو</option>
                        <option value="روفلات">روفلات</option>
                        <option value="كوردو">كوردو</option>
                        <option value="اومبراس">اومبراس</option>
                        <option value="اطاش">اطاش</option>
                        <option value="بورت-اومبراس">بورت امبراس</option>
                        <option value="شويشرا">شويشرا</option>
                        <option value="ميداي">ميداي</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">السعر (دج):</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>حدد الخصائص:</label>
                    <input type="checkbox" id="has_color" onclick="togglePropertyInput('color')"> لون
                    <input type="checkbox" id="has_type" onclick="togglePropertyInput('type')"> نوع
                    <input type="checkbox" id="has_diameter" onclick="togglePropertyInput('diameter')"> قطر
                </div>
                <div class="form-group" id="color-input" style="display:none;">
                    <label for="color">الألوان (افصل بينها بفاصلة):</label>
                    <input type="text" id="color" name="color" class="form-control">
                </div>
                <div class="form-group" id="type-input" style="display:none;">
                    <label for="type">الأنواع (افصل بينها بفاصلة):</label>
                    <input type="text" id="type" name="type" class="form-control">
                </div>
                <div class="form-group" id="diameter-input" style="display:none;">
                    <label for="diameter">الأقطار (افصل بينها بفاصلة):</label>
                    <input type="text" id="diameter" name="diameter" class="form-control">
                </div>
                <div class="form-group">
                    <label for="file">صور / فيديوهات (10 كحد أقصى):</label>
                    <input type="file" id="file" name="file[]" class="form-control" accept="image/*, video/*" multiple required>
                </div>
                <div class="button-container">
                    <button type="submit">إضافة</button>
                    <button type="button" onclick="closeAddProductModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Panneau de modification de produit (Modal) -->
    <div class="modal" id="editProductModal">
        <div class="modal-content">
            <h2>تعديل المنتج</h2>
            <form id="editProductForm" method="POST" enctype="multipart/form-data" class="add-project-form">
                <input type="hidden" name="update_product" value="1">
                <input type="hidden" name="product_id" id="edit_product_id">
                <div class="form-group">
                    <label for="edit_title">العنوان:</label>
                    <input type="text" id="edit_title" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">الوصف:</label>
                    <textarea id="edit_description" name="description" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_category">الفئة:</label>
                    <select id="edit_category" name="category" class="form-control" required>
                        <option value="ترانقل">ترانقل</option>
                        <option value="موتيف">موتيف</option>
                        <option value="سيبور">سيبور</option>
                        <option value="بوشون">بوشون</option>
                        <option value="انو">انو</option>
                        <option value="روفلات">روفلات</option>
                        <option value="كوردو">كوردو</option>
                        <option value="اومبراس">اومبراس</option>
                        <option value="اطاش">اطاش</option>
                        <option value="بورت-اومبراس">بورت امبراس</option>
                        <option value="شويشرا">شويشرا</option>
                        <option value="ميداي">ميداي</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_price">السعر (دج):</label>
                    <input type="number" id="edit_price" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>حدد الخصائص:</label>
                    <input type="checkbox" id="edit_has_color" onclick="toggleEditPropertyInput('color')"> لون
                    <input type="checkbox" id="edit_has_type" onclick="toggleEditPropertyInput('type')"> نوع
                    <input type="checkbox" id="edit_has_diameter" onclick="toggleEditPropertyInput('diameter')"> قطر
                </div>
                <div class="form-group" id="edit_color-input" style="display:none;">
                    <label for="edit_color">الألوان (افصل بينها بفاصلة):</label>
                    <input type="text" id="edit_color" name="color" class="form-control">
                </div>
                <div class="form-group" id="edit_type-input" style="display:none;">
                    <label for="edit_type">الأنواع (افصل بينها بفاصلة):</label>
                    <input type="text" id="edit_type" name="type" class="form-control">
                </div>
                <div class="form-group" id="edit_diameter-input" style="display:none;">
                    <label for="edit_diameter">الأقطار (افصل بينها بفاصلة):</label>
                    <input type="text" id="edit_diameter" name="diameter" class="form-control">
                </div>
                <div class="form-group">
                    <label for="edit_file">صور / فيديوهات (10 كحد أقصى، اترك فارغًا للاحتفاظ بالملفات الحالية):</label>
                    <input type="file" id="edit_file" name="file[]" class="form-control" accept="image/*, video/*" multiple>
                </div>
                <div class="form-group">
                    <label>الصور / الفيديوهات الحالية:</label>
                    <div id="current_images"></div>
                </div>
                <div class="button-container">
                    <button type="submit">تحديث</button>
                    <button type="button" onclick="closeEditProductModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Passer les données PHP à JavaScript -->
    <script>
        const projectsData = <?php echo json_encode($projects); ?>;
        const ordersData = <?php echo json_encode($orders); ?>;

        // Script pour gérer les notifications
        document.addEventListener('DOMContentLoaded', () => {
            const notifications = document.querySelectorAll('.success-notification, .error-notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.remove();
                }, 5000);
            });
        });

        // Fonctions pour ouvrir/fermer le panneau d'ajout
        function openAddProductModal() {
            document.getElementById('addProductModal').style.display = 'flex';
        }

        function closeAddProductModal() {
            document.getElementById('addProductModal').style.display = 'none';
        }

        // Fonctions pour ouvrir/fermer le panneau de modification
        function openEditProductModal(productId) {
            fetch(`dashboard.php?action=get_product&id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    document.getElementById('edit_product_id').value = data.id;
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_description').value = data.description;
                    document.getElementById('edit_category').value = data.category;
                    document.getElementById('edit_price').value = data.price;

                    document.getElementById('edit_has_color').checked = !!data.color;
                    document.getElementById('edit_color').value = data.color || '';
                    document.getElementById('edit_color-input').style.display = data.color ? 'block' : 'none';

                    document.getElementById('edit_has_type').checked = !!data.type;
                    document.getElementById('edit_type').value = data.type || '';
                    document.getElementById('edit_type-input').style.display = data.type ? 'block' : 'none';

                    document.getElementById('edit_has_diameter').checked = !!data.diameter;
                    document.getElementById('edit_diameter').value = data.diameter || '';
                    document.getElementById('edit_diameter-input').style.display = data.diameter ? 'block' : 'none';

                    const currentImagesDiv = document.getElementById('current_images');
                    currentImagesDiv.innerHTML = '';
                    if (data.image_urls) {
                        data.image_urls.split(',').forEach(url => {
                            const img = document.createElement('img');
                            img.src = url;
                            img.alt = 'صورة المنتج';
                            img.style.width = '100px';
                            img.style.height = 'auto';
                            img.style.margin = '5px';
                            currentImagesDiv.appendChild(img);
                        });
                    }

                    document.getElementById('editProductModal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('حدث خطأ أثناء تحميل بيانات المنتج.');
                });
        }

        function closeEditProductModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }

        // Gestion des champs de propriétés
        function togglePropertyInput(prop) {
            const checkbox = document.getElementById(`has_${prop}`);
            const inputDiv = document.getElementById(`${prop}-input`);
            inputDiv.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleEditPropertyInput(prop) {
            const checkbox = document.getElementById(`edit_has_${prop}`);
            const inputDiv = document.getElementById(`edit_${prop}-input`);
            inputDiv.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
    <script src="./dash7.js"></script>
</body>
</html>