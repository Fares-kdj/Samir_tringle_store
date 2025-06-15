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
    displayCart(); // 🟢 Affiche immédiatement le panier après ajout
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
    notification.innerHTML = `تم اضافة&nbsp;<strong>${productTitle}</strong>&nbsp;الى السلة`;

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
        // Fonction pour afficher une notification
        function showCartNotification(message) {
            // Supprimer une notification existante s’il y en a une
            const oldNotification = document.querySelector('.cart-notification');
            if (oldNotification) oldNotification.remove();
        
            const notification = document.createElement('div');
            notification.className = 'cart-notification';
            notification.innerHTML = ` ${message}`;
        
            // Initialiser avec les styles invisibles (à définir dans le CSS)
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            notification.style.transition = 'all 0.3s ease';
        
            document.body.appendChild(notification);
        
            // Forcer le reflow avec requestAnimationFrame avant d'ajouter la classe
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
        
  
  // Événement pour le bouton de commande
  document.getElementById('checkoutButton').addEventListener('click', function() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length === 0) {
      showCartNotification('سلتك فارغة. أضف منتجات قبل المتابعة للدفع.');
      return;
    }
    // Ajoutez ici le reste de votre logique pour passer à la commande
  });
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

// Initialiser le panier au chargement
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    displayCart();
});









document.querySelectorAll('.sidebar a[data-filter]').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
  
      // Gérer la classe active
      document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
  
      const filter = this.getAttribute('data-filter'); // Ex: ".man", ".shoes", "*"
      const filterClass = filter === '*' ? '*' : filter.replace('.', '');
  
      document.querySelectorAll('.iso-box').forEach(box => {
        const match = filter === '*' || box.classList.contains(filterClass);
  
        if (match) {
          box.style.display = 'block';
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
  
















  document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("productModal");
    const modalImage = document.getElementById("modalImage");
    const modalTitle = document.getElementById("modalTitle");
    const modalPrice = document.getElementById("modalPrice");
    const modalDescription = document.getElementById("modalDescription");
    const closeModal = document.querySelector(".close");

    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");

    let currentImages = []; // Liste des images du produit
    let currentIndex = 0; // Index de l'image affichée

    // Gestion du clic sur les images de la galerie
    document.querySelectorAll(".gallery-image").forEach(img => {
        img.addEventListener("click", function () {
            const parent = this.closest(".iso-box");

            // Récupération de toutes les images du même produit
            currentImages = Array.from(parent.querySelectorAll(".gallery-image")).map(image => image.src);
            currentIndex = currentImages.indexOf(this.src);

            // Mise à jour du contenu du modal
            modalTitle.textContent = parent.querySelector("h3").textContent.split(" - ")[0];
            modalPrice.textContent = parent.querySelector("span").textContent;
            modalDescription.textContent = parent.querySelector("p").textContent;

            // Affichage de la première image
            updateModalImage();

            // Afficher le modal
            modal.style.display = "flex";
        });
    });

    // Fonction pour mettre à jour l'image affichée dans le modal
    function updateModalImage() {
        modalImage.src = currentImages[currentIndex];

        // Désactiver les boutons aux extrémités
        prevBtn.style.display = currentIndex === 0 ? "none" : "block";
        nextBtn.style.display = currentIndex === currentImages.length - 1 ? "none" : "block";
    }

    // Gestion des boutons précédent/suivant
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

    // Fermer le modal
    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    console.log('Script chargé');
    const galleries = document.querySelectorAll('.image-gallery');
    console.log('Nombre de galeries trouvées :', galleries.length);

    galleries.forEach(gallery => {
        const images = gallery.querySelectorAll('.gallery-image');
        console.log('Images dans la galerie :', images.length);

        if (images.length > 1) {
            gallery.addEventListener('mouseenter', () => {
                console.log('Survol détecté pour la galerie');
                images.forEach(img => img.classList.remove('active'));
                images[1].classList.add('active');
            });

            gallery.addEventListener('mouseleave', () => {
                console.log('Sortie du survol détectée');
                images.forEach(img => img.classList.remove('active'));
                images[0].classList.add('active');
            });
        } else {
            console.log('Galerie avec moins de 2 images, aucun effet de survol appliqué');
        }
    });
});




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

// Mobile Menu Functionality (le reste de votre code reste inchangé)
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.querySelector('.hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMenu = document.querySelector('.close-menu');
    const mobileNavLinks = document.querySelector('.mobile-nav-links');
    const mobileCartCount = document.getElementById('mobileCartCount');

    // Copier les liens de navigation secondaires dans le menu mobile
    const navLinks = document.querySelectorAll('.nav .btn a');
    navLinks.forEach(link => {
        const mobileLink = document.createElement('a');
        mobileLink.textContent = link.textContent;
        mobileLink.href = link.href;
        mobileNavLinks.appendChild(mobileLink);
    });

    // Synchroniser le compteur de panier mobile avec le compteur principal
    function updateMobileCartCount() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        mobileCartCount.textContent = cart.length;
    }

    updateMobileCartCount();
    const originalUpdateCartCount = updateCartCount;
    updateCartCount = function () {
        originalUpdateCartCount();
        updateMobileCartCount();
    };

    // Ouvrir le menu mobile
    hamburger.addEventListener('click', function () {
        mobileMenu.classList.add('open');
        hamburger.classList.add('active');
    });

    // Fermer le menu mobile
    closeMenu.addEventListener('click', function () {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('active');
    });

    // Fermer le menu mobile en cliquant à l'extérieur
    window.addEventListener('click', function (event) {
        if (!mobileMenu.contains(event.target) && !hamburger.contains(event.target)) {
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('active');
        }
    });

    // Fermer le menu mobile en cliquant sur les liens
    mobileNavLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function () {
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('active');
        });
    });
});