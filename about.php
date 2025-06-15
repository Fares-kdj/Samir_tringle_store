<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>من نحن؟ - سمير ترانقل</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/images/Logo3.png"/>

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/about1.css">
</head>
<body>
    <header>
        <div id="successMessage" class="success-message" style="display:none;">
            تم تنفيذ العملية بنجاح!
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
            <div class="btn"><a href="./index.php">الرئيسية</a></div>
            <div class="btn"><a href="./category.php">منتجاتنا</a></div>
            <div class="btn"><a href="./about.php" class="active">من نحن ؟</a></div>
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
            <!-- Links will be dynamically added by main11.js -->
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
            <!-- About Section -->
            <div class="content-wrapper">
            <section class="about-section">
    <h1 class="text-center mb-4">من نحن؟</h1>
    <!-- Full-width image -->
    <div class="full-width-image">
        <img src="./assets/images/about.png" alt="سمير ترانقل" class="img-fluid">
    </div>
    <div class="row mt-4">
        <!-- About Description -->
        <div class="col-md-6">
            <h3>سمير ترانقل - قصتنا</h3>
            <p>
                يهدف متجر سمير ترانقل الى تقديم منتجات عالية الجودة تلبي احتياجات العملاء في جميع أنحاء البلاد.
                نحن متخصصون في توفير مجموعة واسعة من المنتجات التي تجمع بين الابتكار والأناقة، مع التركيز على الجودة والخدمة الممتازة.
            </p>
            <p>
                منذ بدايتنا، كنا ملتزمين بتقديم تجربة تسوق فريدة من نوعها، سواء من خلال متجرنا الإلكتروني أو من خلال التواصل المباشر مع عملائنا.
                فريقنا المكون من محترفين متحمسين يعمل بجد لضمان رضا العملاء في كل خطوة.
            </p>
        </div>
        <!-- Mission and Vision -->
        <div class="col-md-6">
            <h3>رؤيتنا ورسالتنا</h3>
            <p>
                <strong>رؤيتنا:</strong> أن نكون الخيار الأول للعملاء في الجزائر وخارجها عندما يتعلق الأمر بالمنتجات المبتكرة والموثوقة.
            </p>
            <p>
                <strong>رسالتنا:</strong> تقديم منتجات ذات جودة عالية بأسعار تنافسية، مع التركيز على تجربة العميل والاستدامة.
            </p>
            <p>
                نحن نفخر بكوننا جزءًا من المجتمع الجزائري، ونسعى دائمًا لدعم المبادرات المحلية وتعزيز الاقتصاد الوطني.
            </p>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="./contact.php" class="custom-button">تواصلوا معنا</a>
    </div>
</section>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/main11.js"></script>
    <script>
        // Add 'active' class to the About link in mobile menu
        document.addEventListener('DOMContentLoaded', function () {
            const mobileNavLinks = document.querySelector('.mobile-nav-links');
            const observer = new MutationObserver(function () {
                mobileNavLinks.querySelectorAll('a').forEach(link => {
                    if (link.href.includes('about.php')) {
                        link.classList.add('active');
                    }
                });
            });
            observer.observe(mobileNavLinks, { childList: true });
        });
    </script>
</body>
</html>