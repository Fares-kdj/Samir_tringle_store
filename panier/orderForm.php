<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تأكيد الطلب - سلة التسوق</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/Logo3.png"/>
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style5.css">
    <style>
        body {
            background-image: url("../assets/bg.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .order-container {
            max-width: 1200px;
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
        /* Conteneur des miniatures */
        .thumbnails {
            display: flex;
            flex-wrap: nowrap; /* Pas de retour à la ligne sur desktop */
            justify-content: center; /* Centrer les miniatures */
            gap: 8px; /* Espacement entre les miniatures */
            overflow: visible; /* Toutes les miniatures visibles */
            margin-top: 10px;
        }
        /* Style des miniatures */
        .thumbnail {
            width: 50px; /* Taille pour desktop */
            height: 50px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 5px;
            transition: border 0.3s ease, transform 0.2s ease;
            touch-action: manipulation; /* Améliore les interactions tactiles */
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
            touch-action: manipulation;
        }
        .dot.active {
            background-color: #FFD700;
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
        .product-item {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .main-image {
                height: 200px;
            }
            .thumbnails {
                flex-wrap: wrap; /* Activer le retour à la ligne */
                justify-content: center; /* Centrer les miniatures */
                gap: 8px;
                max-width: 308px; /* 7 miniatures de 40px + 6 espaces de 8px = 280px + marge */
                margin: 0 auto; /* Centrer le conteneur */
            }
            .thumbnail {
                width: 40px; /* Taille pour mobile */
                height: 40px;
                flex: 0 0 40px; /* Fixer la taille pour éviter l'étirement */
                z-index: 1; /* S'assurer que les miniatures sont cliquables */
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
    <!-- Navbar -->
    <nav class="custom-navbar">
        <div class="nav-container">
            <a class="brand" href="../index.php">
                <img src="../assets/images/Logo.png" alt="Logo سمير ترانقل" class="brand-logo">
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
                <div class="cart admin" onclick="window.location.href='../Dash/admin/login.php'; event.stopPropagation();">
                    <i class="fas fa-user-cog"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="order-container">
        <!-- Détails des produits -->
        <div class="product-details">
            <h2>تفاصيل الطلب</h2>
            <div id="orderDetails"></div>
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
                <div id="productOptions"></div>
                <button type="submit" class="order-btn">تأكيد الطلب</button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Charger les données du panier et récupérer les détails des produits
        document.addEventListener("DOMContentLoaded", function() {
            let orderDetails = JSON.parse(localStorage.getItem('orderDetails')) || [];
            let orderDetailsContainer = document.getElementById("orderDetails");
            let productOptionsContainer = document.getElementById("productOptions");
            let totalAmount = 0;

            if (orderDetails.length === 0) {
                orderDetailsContainer.innerHTML = "<p class='error-message'>سلتك فارغة. لا يمكنك المتابعة للطلب.</p>";
                document.getElementById("orderForm").style.display = "none";
                return;
            }

            // Récupérer les IDs des produits
            const productIds = orderDetails.map(item => item.id);

            // Récupérer les détails des produits via AJAX
            fetch('./getProductDetails.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_ids: productIds })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    orderDetailsContainer.innerHTML = "<p class='error-message'>خطأ في تحميل تفاصيل المنتجات.</p>";
                    document.getElementById("orderForm").style.display = "none";
                    return;
                }

                const products = data.products;

                // Afficher les détails des produits
                products.forEach((product, index) => {
                    const orderItem = orderDetails.find(item => item.id == product.id);
                    const total = product.price * orderItem.quantity;
                    totalAmount += total;

                    // Normaliser les image_urls
                    const normalizedImageUrls = product.image_urls.map(url => '../Dash/uploads/' + url.split('/').pop());

                    // Détails du produit avec image principale, miniatures et points de navigation
                    let productRow = `
                        <div class="product-item" data-product-id="${product.id}">
                            <h3>${orderItem.title}</h3>
                            <div class="product-images" data-carousel-id="${index}">
                                ${normalizedImageUrls.length > 0 ? `
                                    <div class="main-image">
                                        <img id="mainImage_${index}" src="${normalizedImageUrls[0]}" alt="Product Image" style="width: 100%; height: 300px; object-fit: cover; border-radius: 5px;">
                                    </div>
                                    <div class="thumbnails">
                                        ${normalizedImageUrls.map((url, imgIndex) => `
                                            <img src="${url}" alt="Thumbnail" class="thumbnail" data-index="${imgIndex}" data-carousel-id="${index}" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer; border: ${imgIndex === 0 ? '2px solid #FFD700' : '1px solid #ddd'};">
                                        `).join('')}
                                    </div>
                                    <div class="carousel-dots">
                                        ${normalizedImageUrls.map((url, imgIndex) => `
                                            <span class="dot ${imgIndex === 0 ? 'active' : ''}" data-index="${imgIndex}" data-carousel-id="${index}"></span>
                                        `).join('')}
                                    </div>
                                ` : `
                                    <div class="main-image">
                                        <img id="mainImage_${index}" src="../assets/default-image.png" alt="No Image" style="width: 100%; height: 300px; object-fit: cover; border-radius: 5px;">
                                    </div>
                                    <div class="thumbnails">
                                        <img src="../assets/default-image.png" alt="Thumbnail" class="thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    </div>
                                `}
                            </div>
                            <p><strong>السعر:</strong> ${product.price} دج</p>
                            <p><strong>الكمية:</strong> ${orderItem.quantity}</p>
                            <p><strong>الإجمالي:</strong> ${total} دج</p>
                            ${product.colors.length > 0 ? `<p><strong>الألوان المتوفرة:</strong> ${product.colors.join(', ')}</p>` : ''}
                            ${product.types.length > 0 ? `<p><strong>الأنواع المتوفرة:</strong> ${product.types.join(', ')}</p>` : ''}
                            ${product.diameters.length > 0 ? `<p><strong>الأقطار المتوفرة:</strong> ${product.diameters.join(', ')}</p>` : ''}
                        </div>
                    `;
                    orderDetailsContainer.innerHTML += productRow;

                    // Options du produit dans le formulaire
                    let productOptions = `
                        <div class="product-options" data-product-id="${product.id}">
                            <h4>${orderItem.title}</h4>
                            ${product.colors.length > 0 ? `
                                <div class="form-group">
                                    <label for="product_color_${product.id}">اللون:</label>
                                    <select id="product_color_${product.id}" name="products[${product.id}][color]" class="form-control" required>
                                        <option value="">اختر اللون</option>
                                        ${product.colors.map(color => `<option value="${color}">${color}</option>`).join('')}
                                    </select>
                                </div>
                            ` : ''}
                            ${product.types.length > 0 ? `
                                <div class="form-group">
                                    <label for="product_type_${product.id}">النوع:</label>
                                    <select id="product_type_${product.id}" name="products[${product.id}][type]" class="form-control" required>
                                        <option value="">اختر النوع</option>
                                        ${product.types.map(type => `<option value="${type}">${type}</option>`).join('')}
                                    </select>
                                </div>
                            ` : ''}
                            ${product.diameters.length > 0 ? `
                                <div class="form-group">
                                    <label for="product_diameter_${product.id}">القطر:</label>
                                    <select id="product_diameter_${product.id}" name="products[${product.id}][diameter]" class="form-control" required>
                                        <option value="">اختر القطر</option>
                                        ${product.diameters.map(diameter => `<option value="${diameter}">${diameter}</option>`).join('')}
                                    </select>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    productOptionsContainer.innerHTML += productOptions;
                });

                // Afficher le total de la commande
                let totalRow = `
                    <div class="order-total">
                        <p><strong>إجمالي الطلب: ${totalAmount} دج</strong></p>
                    </div>
                `;
                orderDetailsContainer.innerHTML += totalRow;

                // Ajouter les gestionnaires d'événements pour les miniatures et les points
                const thumbnails = document.querySelectorAll('.thumbnail');
                const dots = document.querySelectorAll('.dot');

                const handleInteraction = (element) => {
                    const index = parseInt(element.getAttribute('data-index'), 10);
                    const carouselId = element.getAttribute('data-carousel-id');
                    const productImages = document.querySelectorAll(`.product-images[data-carousel-id="${carouselId}"] .thumbnail`);
                    const mainImage = document.getElementById(`mainImage_${carouselId}`);
                    const images = Array.from(productImages).map(img => img.src);

                    if (images.length === 0) return;

                    // Mettre à jour l'image principale
                    mainImage.src = images[index];

                    // Mettre à jour les bordures des miniatures
                    productImages.forEach((thumb, i) => {
                        thumb.style.border = i === index ? '2px solid #FFD700' : '1px solid #ddd';
                    });

                    // Mettre à jour les points de navigation
                    const carouselDots = document.querySelectorAll(`.product-images[data-carousel-id="${carouselId}"] .dot`);
                    carouselDots.forEach((dot, i) => {
                        dot.classList.toggle('active', i === index);
                    });
                };

                thumbnails.forEach(thumbnail => {
                    thumbnail.addEventListener('click', () => handleInteraction(thumbnail));
                    thumbnail.addEventListener('touchend', (e) => {
                        e.preventDefault();
                        handleInteraction(thumbnail);
                    });
                });

                dots.forEach(dot => {
                    dot.addEventListener('click', () => handleInteraction(dot));
                    dot.addEventListener('touchend', (e) => {
                        e.preventDefault();
                        handleInteraction(dot);
                    });
                });
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
                orderDetailsContainer.innerHTML = "<p class='error-message'>خطأ في تحميل تفاصيل المنتجات.</p>";
                document.getElementById("orderForm").style.display = "none";
            });
        });

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
            const orderDetails = JSON.parse(localStorage.getItem('orderDetails')) || [];
            formData.append('orderDetails', JSON.stringify(orderDetails));

            fetch('./submitorder.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Statut HTTP:', response.status);
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Données JSON:', data);
                showNotification(data.message, data.success);
                if (data.success) {
                    document.getElementById('orderForm').reset();
                    localStorage.removeItem('cart');
                    localStorage.removeItem('orderDetails');

                    // Ajouter un bouton de retour
                    const backButton = document.createElement('button');
                    backButton.textContent = 'العودة إلى الصفحة الرئيسية';
                    backButton.classList.add('order-btn');
                    backButton.onclick = function() {
                        window.location.href = '../index.php';
                    };
                    document.querySelector('.order-form').appendChild(backButton);
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