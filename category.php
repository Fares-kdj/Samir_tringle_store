<?php
// Inclure la connexion à la base de données
include './Dash/includes/db.php';

// Liste des catégories (correspondant à celles de votre tableau de bord)
$categories = [
    'ترانقل' => 'ترانقل',
    'موتيف' => 'موتيف',
    'سيبور' => 'سيبور',
    'بوشون' => 'بوشون',
    'انو' => 'انو',
    'روفلات' => 'روفلات',
    'كوردو' => 'كوردو',
    'اومبراس' => 'اومبراس',
    'اطاش' => 'اطاش',
    'بورت-اومبراس' => 'بورت امبراس',
    'شويشرا' => 'شويشرا',
    'ميداي' => 'ميداي'
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جميع الفئات - سمير ترانقل</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/images/Logo3.png"/>

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="./assets/css/category3.css">
 
</head>
<body>
    <!-- Navbar -->
    <nav class="custom-navbar">
        <div class="nav-container">
        <a class="brand" href="./index.php">
                <img src="./assets/images/Logo.png" alt="Logo سمير ترانقل" class="brand-logo">
            </a>
            <!-- Hamburger Icon for Mobile -->
            <button class="hamburger" aria-label="Toggle mobile menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <form class="search-form">
                <button class="search-button" type="submit"><i class="fa fa-search"></i></button>
                <input type="text" id="searchBar" placeholder="ابحث عن منتج..." class="search-input" />
            </form>
            <div class="nav-icons">
                <div class="cart" onclick="openCart(); event.stopPropagation();">
                    <i class="fas fa-shopping-cart cart-icon"></i>
                    <span id="cart-count">0</span>
                </div>
                <div class="cart admin" onclick="window.location.href='./Dash/admin/login.php';">
                    <i class="fas fa-user-cog"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Secondary Navigation (Liste des catégories) -->
    <div class="nav">
        <div class="container">
            <!-- Bouton pour afficher tous les produits -->
            <div class="btn filter active" data-filter="*">الكل</div>
            <?php foreach ($categories as $key => $category): ?>
                <div class="btn filter" data-filter=".<?php echo $key; ?>">
                    <?php echo $category; ?>
                </div>
            <?php endforeach; ?>
            <svg class="outline" overflow="visible" width="1200" height="60" viewBox="0 0 1200 60" xmlns="http://www.w3.org/2000/svg">
                <rect class="rect" pathLength="100" x="0" y="0" width="1200" height="60" fill="transparent" stroke-width="5"></rect>
            </svg>
        </div>
    </div>

    <!-- Cart side panel -->
    <div class="cart-panel" id="cartPanel">
        <button class="close-btn" onclick="closeCart()">×</button>
        <h1>سلة التسوق</h1>
        <table class="projects-table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>المجموع</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody id="cartTableBody">
                <!-- Products will be injected here -->
            </tbody>
        </table>
        <p id="cartTotal" class="success-message">إجمالي السلة: 0 دج</p>
        <button id="checkoutButton" class="order-btn">المتابعة للدفع</button>
    </div>

    <!-- Mobile Menu Panel -->
    <div class="mobile-menu" id="mobileMenu">
        <button class="close-menu" aria-label="Close mobile menu">×</button>
        <div class="mobile-brand">سمير ترانقل</div>
        <form class="mobile-search-form">
        <button type="submit" class="search-button"><i class="fa fa-search"></i></button>
            <input type="text" class="search-input" id="mobileSearchBar" placeholder="ابحث عن منتج...">
        </form>
        <div class="mobile-nav-links">
            <!-- Category filters will be injected dynamically via JS -->
        </div>
        <div class="mobile-cart-link">
            <button class="cart-toggle" onclick="openCart()">عرض السلة (<span id="mobileCartCount">0</span>)</button>
        </div>
        <div class="mobile-admin-link">
            <a href="./Dash/admin/login.php"><i class="fas fa-user-cog"></i> لوحة التحكم</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Portfolio Section -->
        <section id="category-products" class="portfolio-section">
            <div class="portfolio-container">
                <div class="iso-box-section">
                    <div class="iso-box-wrapper">
                        <?php
                        $sql = "SELECT * FROM projects";
                        $stmt = $conn->query($sql);
                        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($projects)) {
                            foreach ($projects as $project):
                                $image_urls = explode(',', $project['image_urls']);
                        ?>
                                <div class="iso-box <?php echo $project['category']; ?>" data-project-id="<?php echo $project['id']; ?>">
                                    <div class="image-gallery">
                                        <?php
                                        foreach ($image_urls as $index => $image_url):
                                            $filePath = './Dash/uploads/' . basename($image_url);
                                        ?>
                                            <img class="gallery-image <?php echo $index === 0 ? 'active' : ''; ?>" 
                                                 src="<?php echo $filePath; ?>" 
                                                 alt="Image <?php echo $index + 1; ?>">
                                        <?php endforeach; ?>
                                    </div>
                                    <h3>
                                        <?php echo $project['title']; ?> - 
                                        <span class="price"><?php echo number_format($project['price'], 2); ?> دج</span>
                                    </h3>
                                    <p><?php echo $project['description']; ?></p>
                                    <div class="buttons-container">
                                        <button class="custom-button" onclick="location.href='order.php?product_id=<?php echo $project['id']; ?>'">
                                            اطلب الان
                                        </button>
                                        <button class="custom-button" onclick="addToCart('<?php echo $project['id']; ?>', '<?php echo $project['title']; ?>', <?php echo $project['price']; ?>)">
                                            <i class="fas fa-cart-plus"></i> اضف الى السلة
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach;
                        } else {
                            echo "<p class='no-projects'>لا يوجد منتجات حاليا.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
            <div class="carousel">
                <img id="modalImage" src="" alt="Product Image">
                <button id="prevBtn">❯</button>
                <button id="nextBtn">❮</button>
            </div>
            <h2 id="modalTitle"></h2>
            <span id="modalPrice"></span>
            <p id="modalDescription"></p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/categori.js"></script>
</body>
</html>