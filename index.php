<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Samir tringle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/images/Logo3.png"/>
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/style5.css">
</head>
<body>
    <header>
        <div id="successMessage" class="success-message" style="display:none;">
            <span id="addedProductTitle"></span> تم اضافة المنتج الى سلتك.
        </div>
    </header>

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
                <div class="cart admin" onclick="window.location.href='./Dash/admin/login.php'; event.stopPropagation();">
                    <i class="fas fa-user-cog"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Secondary Navigation -->
    <div class="nav">
        <div class="container">
            <div class="btn"><a href="#">الرئيسية</a></div>
            <div class="btn"><a href="./category.php">منتجاتنا</a></div>
            <div class="btn"><a href="./about.php">من نحن ؟</a></div>
            <div class="btn"><a href="./contact.php">اتصل بنا</a></div>
            <svg class="outline" overflow="visible" width="400" height="60" viewBox="0 0 400 60" xmlns="http://www.w3.org/2000/svg">
                <rect class="rect" pathLength="100" x="0" y="0" width="400" height="60" fill="transparent" stroke-width="5"></rect>
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
            <!-- Links will be injected dynamically via JS -->
        </div>
        <div class="mobile-cart-link">
            <button class="cart-toggle" onclick="openCart()">عرض السلة (<span id="mobileCartCount">0</span>)</button>
        </div>
        <div class="mobile-admin-link">
            <a href="./Dash/admin/login.php"><i class="fas fa-user-cog"></i> لوحة التحكم</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <div class="layout">
            <!-- Sidebar -->
            <div class="sidebar">
                <a href="#" data-filter=".ترانقل" class="filter">
                    <div class="link-content">
                        <span><img src="./assets/images/tringle.png" alt="icon"></span>ترانقل
                    </div>
                </a>
                <a href="#" data-filter=".موتيف">
                    <div class="link-content">
                        <span><img src="./assets/images/motif.png" alt="icon"></span>موتيف
                    </div>
                </a>
                <a href="#" data-filter=".سيبور">
                    <div class="link-content">
                        <span><img src="./assets/images/bracket.png" alt="icon"></span>سيبور
                    </div>
                </a>
                <a href="#" data-filter=".بوشون">
                    <div class="link-content">
                        <span><img src="./assets/images/bouchon.png" alt="icon"></span>بوشون
                    </div>
                </a>
                <a href="#" data-filter=".انو" class="filter">
                    <div class="link-content">
                        <span><img src="./assets/images/annaux.png" alt="icon"></span>انو
                    </div>
                </a>
                <a href="#" data-filter=".روفلات" class="filter">
                    <div class="link-content">
                        <span><img src="./assets/images/rouflette.png" alt="icon"></span>روفلات
                    </div>
                </a>
                <a href="category.php">
                    <div class="link-content">
                        جميع الفئات
                    </div>
                </a>
            </div>

            <!-- Portfolio Section -->
            <div class="content-wrapper">
                <section id="portfolio" class="portfolio-section">
                    <div class="portfolio-container">
                        <div class="iso-box-section wow fadeIn" data-wow-delay="0.9s">
                            <div class="iso-box-wrapper">
                                <?php
                                include './Dash/includes/db.php';
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
        </div>
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
    <script src="./assets/isotope.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
    <script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/main1.6.js"></script>
</body>
</html>