<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اتصل بنا - سمير ترانقل</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/images/Logo3.png"/>
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/contact1.css">
</head>
<body>
    <header>
        <div id="successMessage" class="success-message" style="display:none;">
            تم إرسال رسالتك بنجاح!
        </div>
    </header>

    <!-- Navbar -->
    <nav class="custom-navbar">
        <div class="nav-container">
        <a class="brand" href="./index.php">
                <img src="./assets/images/logo.png" alt="Logo سمير ترانقل" class="brand-logo">
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
            <div class="btn"><a href="./about.php">من نحن ؟</a></div>
            <div class="btn"><a href="./contact.php" class="active">اتصل بنا</a></div>
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
            <!-- Contact Section -->
            <div class="content-wrapper">
                <section class="contact-section">
                    <h1 class="text-center mb-4">اتصل بنا</h1>
                    <div class="row">
                        <!-- Contact Form -->
                        <div class="col-md-6">
                            <form id="contactForm" class="contact-form">
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم الكامل</label>
                                    <input type="text" class="form-control" id="name" placeholder="أدخل اسمك الكامل" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control" id="email" placeholder="أدخل بريدك الإلكتروني" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="tel" class="form-control" id="phone" placeholder="أدخل رقم هاتفك">
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">رسالتك</label>
                                    <textarea class="form-control" id="message" rows="5" placeholder="اكتب رسالتك هنا..." required></textarea>
                                </div>
                                <button type="submit" class="custom-button">إرسال الرسالة</button>
                            </form>
                        </div>
                        <!-- Contact Info -->
                        <div class="col-md-6 contact-info">
                            <h3>معلومات التواصل</h3>
                            <p>نحن هنا للإجابة على استفساراتك! تواصلوا معنا عبر الوسائل التالية:</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-map-marker-alt"></i> العنوان: بابا حسن، الجزائر العاصمة، الجزائر</li>
                                <li><i class="fas fa-phone"></i> الهاتف: 0554106223</li>
                                <li><i class="fas fa-envelope"></i> البريد الإلكتروني: .....</li>
                                <li><i class="fas fa-clock"></i> ساعات العمل: الأحد - الخميس، 7:00 - 19:00</li>
                            </ul>
                            <div class="social-links mt-4">
                                <a href="https://web.facebook.com/profile.php?id=100077749773778&locale=fr_FR" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com/samir_tringle?igsh=NG5rdmNpcTQzMXcw" class="social-icon"><i class="fab fa-instagram"></i></a>
                                <a href="https://wa.me/+213554106223" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/main1.6.js"></script>
    <script>
        // Basic form submission handling
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';
            successMessage.classList.add('success-notification');
            successMessage.classList.add('show');
            this.reset();
            setTimeout(() => {
                successMessage.style.display = 'none';
                successMessage.classList.remove('show');
            }, 3000);
        });
    </script>
</body>
</html>