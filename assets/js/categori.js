// ... Votre JavaScript existant reste inchangé ...
     // Ajouter un produit au panier
     function addToCart(productId, productTitle, productPrice) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let existingProduct = cart.find(item => item.id === productId);

        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            cart.push({
                id: productId,
                title: productTitle,
                price: productPrice,
                quantity: 1
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        displayCart();
        displaySuccessMessage(productTitle);
    }

    // Mettre à jour le nombre d’articles
    function updateCartCount() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        document.getElementById('cart-count').textContent = cart.length;
    }

    // Afficher la notification
    function displaySuccessMessage(productTitle) {
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.innerHTML = `تم اضافة <strong>${productTitle}</strong> الى السلة`;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 100);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }

    // Ouvrir et fermer le panneau du panier
    function openCart() {
        document.getElementById('cartPanel').classList.add('open');
    }

    function closeCart() {
        document.getElementById('cartPanel').classList.remove('open');
    }

    // Afficher les produits du panier dans le tableau
    function displayCart() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let tableBody = document.getElementById("cartTableBody");
        let totalAmount = 0;

        tableBody.innerHTML = "";

        if (cart.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='5' class='error-message'>سلتك فارغة.</td></tr>";
        } else {
            cart.forEach((item, index) => {
                let totalItem = item.price * item.quantity;
                totalAmount += totalItem;

                let row = `
                    <tr>
                        <td>${item.title}</td>
                        <td>${item.price} دج</td>
                        <td>
                            <button onclick="modifyQuantity(${index}, -1)">-</button>
                            ${item.quantity}
                            <button onclick="modifyQuantity(${index}, 1)">+</button>
                        </td>
                        <td>${totalItem} دج</td>
                        <td>
                            <a href="#" class="delete-link" onclick="removeProduct(${index})">إزالة</a>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }

        document.getElementById("cartTotal").textContent = `إجمالي السلة: ${totalAmount} دج`;
        updateCartCount();
    }

    // Modifier la quantité d’un produit
    function modifyQuantity(index, change) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        if (cart[index]) {
            cart[index].quantity += change;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        displayCart();
    }

    // Supprimer un produit du panier
    function removeProduct(index) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        displayCart();
    }

    // Passage à la commande
    document.getElementById('checkoutButton').addEventListener('click', function() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart.length === 0) {
            showCartNotification('سلتك فارغة. أضف منتجات قبل المتابعة للدفع.');
            return;
        }

        let productDetails = cart.map(item => ({
            id: item.id,
            title: item.title,
            price: item.price,
            quantity: item.quantity,
            total: item.price * item.quantity
        }));

        localStorage.setItem('orderDetails', JSON.stringify(productDetails));
        window.location.href = './panier/orderForm.php';
    });

    // Fonction pour afficher une notification
    function showCartNotification(message) {
        const oldNotification = document.querySelector('.cart-notification');
        if (oldNotification) oldNotification.remove();

        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.innerHTML = ` ${message}`;

        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        notification.style.transition = 'all 0.3s ease';

        document.body.appendChild(notification);

        requestAnimationFrame(() => {
            notification.classList.add('show');
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Initialiser le panier au chargement
    document.addEventListener('DOMContentLoaded', () => {
        updateCartCount();
        displayCart();
    });

    // Filtrage des produits via la barre de navigation (sans Isotope)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn.filter').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                // Gérer la classe active
                document.querySelectorAll('.btn.filter').forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                const filter = this.getAttribute('data-filter'); // Ex: ".*", ".tringle"
                const filterClass = filter === '*' ? '*' : filter.replace('.', '');

                document.querySelectorAll('.iso-box').forEach(box => {
                    const match = filter === '*' || box.classList.contains(filterClass);

                    if (match) {
                        box.style.display = 'inline-flex';
                        box.style.opacity = 0;
                        box.style.transform = 'translateY(20px)';

                        setTimeout(() => {
                            box.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                            box.style.opacity = 1;
                            box.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        box.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                        box.style.opacity = 0;
                        box.style.transform = 'translateY(20px)';

                        setTimeout(() => {
                            box.style.display = 'none';
                        }, 400);
                    }
                });
            });
        });

        // Par défaut, afficher tous les produits
        document.querySelector('.btn.filter[data-filter="*"]').click();
    });

    // Gestion du modal pour les produits
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("productModal");
        const modalImage = document.getElementById("modalImage");
        const modalTitle = document.getElementById("modalTitle");
        const modalPrice = document.getElementById("modalPrice");
        const modalDescription = document.getElementById("modalDescription");
        const closeModal = document.querySelector(".close");

        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        let currentImages = [];
        let currentIndex = 0;

        document.querySelectorAll(".gallery-image").forEach(img => {
            img.addEventListener("click", function () {
                const parent = this.closest(".iso-box");

                currentImages = Array.from(parent.querySelectorAll(".gallery-image")).map(image => image.src);
                currentIndex = currentImages.indexOf(this.src);

                modalTitle.textContent = parent.querySelector("h3").textContent.split(" - ")[0];
                modalPrice.textContent = parent.querySelector("span").textContent;
                modalDescription.textContent = parent.querySelector("p").textContent;

                updateModalImage();

                modal.style.display = "flex";
            });
        });

        function updateModalImage() {
            modalImage.src = currentImages[currentIndex];
            prevBtn.style.display = currentIndex === 0 ? "none" : "block";
            nextBtn.style.display = currentIndex === currentImages.length - 1 ? "none" : "block";
        }

        prevBtn.addEventListener("click", function () {
            if (currentIndex > 0) {
                currentIndex--;
                updateModalImage();
            }
        });

        nextBtn.addEventListener("click", function () {
            if (currentIndex < currentImages.length - 1) {
                currentIndex++;
                updateModalImage();
            }
        });

        closeModal.addEventListener("click", function () {
            modal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });

    // Gestion des galeries d'images (effet de survol)
    document.addEventListener('DOMContentLoaded', function() {
        const galleries = document.querySelectorAll('.image-gallery');

        galleries.forEach(gallery => {
            const images = gallery.querySelectorAll('.gallery-image');

            if (images.length > 1) {
                gallery.addEventListener('mouseenter', () => {
                    images.forEach(img => img.classList.remove('active'));
                    images[1].classList.add('active');
                });

                gallery.addEventListener('mouseleave', () => {
                    images.forEach(img => img.classList.remove('active'));
                    images[0].classList.add('active');
                });
            }
        });
    });

    // Gestion de la recherche

    document.addEventListener("DOMContentLoaded", function () {
        const searchBar = document.getElementById("searchBar");
        const mobileSearchBar = document.getElementById("mobileSearchBar");
        const searchForm = document.querySelector(".search-form");
        const mobileSearchForm = document.querySelector(".mobile-search-form");
    
        // Fonction commune pour effectuer la recherche
        function performSearch(searchTerm) {
            const products = document.querySelectorAll(".iso-box");
            searchTerm = searchTerm.toLowerCase();
    
            products.forEach((product) => {
                const title = product.querySelector("h3").textContent.toLowerCase();
                const description = product.querySelector("p")?.textContent.toLowerCase() || "";
    
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    product.style.display = "block";
                } else {
                    product.style.display = "none";
                }
            });
        }
    
        // Recherche en temps réel (input) pour la barre de recherche desktop
        searchBar.addEventListener("input", function () {
            performSearch(searchBar.value);
        });
    
        // Recherche en temps réel (input) pour la barre de recherche mobile
        mobileSearchBar.addEventListener("input", function () {
            performSearch(mobileSearchBar.value);
        });
    
        // Gestion de la soumission du formulaire desktop
        searchForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Empêche le rechargement de la page
            performSearch(searchBar.value);
        });
    
        // Gestion de la soumission du formulaire mobile
        mobileSearchForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Empêche le rechargement de la page
            performSearch(mobileSearchBar.value);
        });
    });
// Mobile Menu Functionality
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.querySelector('.hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMenu = document.querySelector('.close-menu');
    const mobileNavLinks = document.querySelector('.mobile-nav-links');
    const mobileCartCount = document.getElementById('mobileCartCount');
    const mobileSearchBar = document.getElementById('mobileSearchBar');
  
    // Copy category filter buttons to mobile menu
    const filterButtons = document.querySelectorAll('.nav .btn.filter');
    filterButtons.forEach(button => {
      const mobileButton = document.createElement('a');
      mobileButton.textContent = button.textContent;
      mobileButton.setAttribute('data-filter', button.getAttribute('data-filter'));
      mobileButton.classList.add('filter');
      if (button.classList.contains('active')) {
        mobileButton.classList.add('active');
      }
      mobileNavLinks.appendChild(mobileButton);
    });
  
    // Synchronize mobile cart count with main cart count
    function updateMobileCartCount() {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      mobileCartCount.textContent = cart.length;
    }
  
    // Call initially and after cart updates
    updateMobileCartCount();
    const originalUpdateCartCount = updateCartCount;
    updateCartCount = function () {
      originalUpdateCartCount();
      updateMobileCartCount();
    };
  
    // Open mobile menu
    hamburger.addEventListener('click', function () {
      mobileMenu.classList.add('open');
      hamburger.classList.add('active');
    });
  
    // Close mobile menu
    closeMenu.addEventListener('click', function () {
      mobileMenu.classList.remove('open');
      hamburger.classList.remove('active');
    });
  
    // Close mobile menu when clicking outside
    window.addEventListener('click', function (event) {
      if (!mobileMenu.contains(event.target) && !hamburger.contains(event.target)) {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('active');
      }
    });
  
    // Handle category filters in mobile menu
    mobileNavLinks.querySelectorAll('a.filter').forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault();
  
        // Update active state
        mobileNavLinks.querySelectorAll('a.filter').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
  
        const filter = this.getAttribute('data-filter');
        const filterClass = filter === '*' ? '*' : filter.replace('.', '');
  
        document.querySelectorAll('.iso-box').forEach(box => {
          const match = filter === '*' || box.classList.contains(filterClass);
          if (match) {
            box.style.display = 'inline-flex';
            box.style.opacity = 0;
            box.style.transform = 'translateY(20px)';
            setTimeout(() => {
              box.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
              box.style.opacity = 1;
              box.style.transform = 'translateY(0)';
            }, 10);
          } else {
            box.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            box.style.opacity = 0;
            box.style.transform = 'translateY(20px)';
            setTimeout(() => {
              box.style.display = 'none';
            }, 400);
          }
        });
  
        // Close mobile menu after clicking a filter
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('active');
      });
    });
  
    // Mobile search functionality
    mobileSearchBar.addEventListener('input', function () {
      const searchTerm = mobileSearchBar.value.toLowerCase();
      const products = document.querySelectorAll('.iso-box');
  
      products.forEach(product => {
        const title = product.querySelector('h3').textContent.toLowerCase();
        const description = product.querySelector('p')?.textContent.toLowerCase() || '';
        product.style.display = title.includes(searchTerm) || description.includes(searchTerm) ? 'inline-flex' : 'none';
      });
    });
  });
































































  