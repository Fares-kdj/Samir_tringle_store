// Gestion du mode sombre/clair
const modeSwitch = document.querySelector('.mode-switch');
if (modeSwitch) {
    modeSwitch.addEventListener('click', () => {
        document.documentElement.classList.toggle('light');
        modeSwitch.classList.toggle('active');
        console.log('Mode sombre/clair basculé');
    });
} else {
    console.error('Élément .mode-switch non trouvé');
}

// Gestion de l'affichage de la liste et de la grille
const viewButtons = document.querySelectorAll('.action-button.list, .action-button.grid');
const productsWrapper = document.querySelector('.products-area-wrapper');
if (viewButtons && productsWrapper) {
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            productsWrapper.classList.remove('tableView', 'gridView');
            productsWrapper.classList.add(button.classList.contains('list') ? 'tableView' : 'gridView');
            console.log('Vue changée à :', button.classList.contains('list') ? 'tableView' : 'gridView');
        });
    });
} else {
    console.error('Éléments .action-button ou .products-area-wrapper non trouvés');
}

// Gestion du menu de filtrage et de la barre de recherche
const filterButton = document.querySelector('.jsFilter');
const filterMenu = document.querySelector('.filter-menu');
const filterCategory = document.querySelector('#filter-category');
const applyButton = document.querySelector('.filter-button.apply');
const resetButton = document.querySelector('.filter-button.reset');
const searchBar = document.querySelector('.search-bar');

if (filterButton && filterMenu && filterCategory && applyButton && resetButton && searchBar && productsWrapper) {
    let currentCategory = 'جميع الفئات';

    filterButton.addEventListener('click', () => {
        filterMenu.classList.toggle('active');
        console.log('Menu de filtrage basculé');
    });

    document.addEventListener('click', (e) => {
        if (!filterButton.contains(e.target) && !filterMenu.contains(e.target)) {
            filterMenu.classList.remove('active');
            console.log('Menu de filtrage fermé');
        }
    });

    function populateCategories(view) {
        const data = view === 'products' ? projectsData : ordersData;
        const categories = [...new Set(data.map(item => item.category))];
        const options = ['جميع الفئات', ...categories];
        filterCategory.innerHTML = options.map(category => `
            <option value="${category}">${category}</option>
        `).join('');
    }

    function applyFilters(selectedCategory, searchText) {
        const rows = document.querySelectorAll('.products-row');
        if (rows.length === 0) {
            console.warn('Aucune ligne de produits trouvée pour le filtrage');
            return;
        }

        searchText = searchText.toLowerCase().trim();

        rows.forEach(row => {
            const category = row.querySelector('.product-cell.category')?.textContent || '';
            const title = row.querySelector('.product-title')?.textContent.toLowerCase() || 
                          row.querySelector('.product-cell.product-name')?.textContent.toLowerCase() || '';

            const matchesCategory = selectedCategory === 'جميع الفئات' || category === selectedCategory;
            const matchesSearch = searchText === '' || 
                                 title.includes(searchText) || 
                                 category.toLowerCase().includes(searchText);

            row.style.display = matchesCategory && matchesSearch ? '' : 'none';
        });

        console.log('Filtres appliqués - Catégorie:', selectedCategory, 'Recherche:', searchText);
    }

    applyButton.addEventListener('click', () => {
        currentCategory = filterCategory.value;
        applyFilters(currentCategory, searchBar.value);
        filterMenu.classList.remove('active');
    });

    resetButton.addEventListener('click', () => {
        filterCategory.value = 'جميع الفئات';
        currentCategory = 'جميع الفئات';
        searchBar.value = '';
        applyFilters(currentCategory, '');
        filterMenu.classList.remove('active');
        console.log('Filtres réinitialisés');
    });

    searchBar.addEventListener('input', (e) => {
        const searchText = e.target.value;
        applyFilters(currentCategory, searchText);
        console.log('Recherche effectuée avec le terme :', searchText);
    });
} else {
    console.error('Éléments .jsFilter, .filter-menu, #filter-category, .filter-button.apply, .filter-button.reset, .search-bar ou .products-area-wrapper non trouvés');
}

// Fonction pour générer le HTML des produits
function generateProductsHTML(projects, view) {
    let html = `
        <div class="products-header">
            <div class="product-cell product-id">رقم المنتج</div>
            <div class="product-cell product-name">اسم المنتج</div>
            <div class="product-cell description">الوصف</div>
            <div class="product-cell category">الفئة</div>
            <div class="product-cell price">السعر (دج)</div>
            <div class="product-cell color">الألوان</div>
            <div class="product-cell type">الأنواع</div>
            <div class="product-cell diameter">الأقطار</div>
            <div class="product-cell actions">الإجراءات</div>
        </div>
    `;

    if (projects.length === 0) {
        html += `
            <div class="products-row">
                <div class="product-cell error-message" style="grid-column: span 9;">لا توجد منتجات لعرضها.</div>
            </div>
        `;
    } else {
        projects.forEach(project => {
            const image_urls = project.image_urls ? project.image_urls.split(',') : [];
            const first_image = image_urls.length > 0 ? image_urls[0] : '../../assets/default-image.png';
            const status = project.price > 0 ? 'active' : 'disabled';
            const colors = project.color ? project.color.split(',').join(', ') : 'غير متوفر';
            const types = project.type ? project.type.split(',').join(', ') : 'غير متوفر';
            const diameters = project.diameter ? project.diameter.split(',').join(', ') : 'غير متوفر';
            html += `
                <div class="products-row">
                    <div class="product-cell product-id">${project.id}</div>
                    <div class="product-cell product-name">
                        <img src="${first_image}" alt="${project.title}">
                        ${project.title}
                    </div>
                    <div class="product-cell description">${project.description || 'غير متوفر'}</div>
                    <div class="product-cell category">${project.category}</div>
                    <div class="product-cell price">${parseFloat(project.price).toFixed(2)} دج</div>
                    <div class="product-cell color">${colors}</div>
                    <div class="product-cell type">${types}</div>
                    <div class="product-cell diameter">${diameters}</div>
                    <div class="product-cell actions">
                        <a href="#" class="edit-link" onclick="openEditProductModal(${project.id}); return false;">تعديل</a>
                        <a href="?delete_id=${project.id}" class="delete-link" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">حذف</a>
                    </div>
                    <div class="product-info-grid">
                        <img src="${first_image}" alt="${project.title}" class="product-image-grid">
                        <div class="product-details">
                            <h3 class="product-title">${project.title}</h3>
                            <p class="product-detail"><span>الفئة:</span> ${project.category}</p>
                            <p class="product-detail status" data-status="${status}"><span>الحالة:</span> ${status === 'active' ? 'نشط' : 'معطل'}</p>
                            <p class="product-detail"><span>السعر:</span> ${parseFloat(project.price).toFixed(2)} دج</p>
                            <p class="product-detail"><span>الألوان:</span> ${colors}</p>
                            <p class="product-detail"><span>الأنواع:</span> ${types}</p>
                            <p class="product-detail"><span>الأقطار:</span> ${diameters}</p>
                            <div class="product-actions">
                                <a href="#" class="edit-link" onclick="openEditProductModal(${project.id}); return false;">تعديل</a>
                                <a href="?delete_id=${project.id}" class="delete-link" onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">حذف</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    return html;
}

// Fonction pour générer le HTML des commandes
function generateOrdersHTML(orders, view) {
    let html = `
        <div class="products-header">
            <div class="product-cell order-id">رقم الطلب</div>
            <div class="product-cell product-name">اسم المنتج</div>
            <div class="product-cell price">السعر (دج)</div>
            <div class="product-cell quantity">الكمية</div>
            <div class="product-cell color">اللون</div>
            <div class="product-cell type">النوع</div>
            <div class="product-cell diameter">القطر</div>
            <div class="product-cell total-price">السعر الإجمالي (دج)</div>
            <div class="product-cell customer-name">اسم العميل</div>
            <div class="product-cell phone">رقم الهاتف</div>
            <div class="product-cell address">العنوان</div>
            <div class="product-cell order-date">تاريخ الطلب</div>
            <div class="product-cell status-cell">تأكيد الطلب</div>
            <div class="product-cell actions">الإجراءات</div>
        </div>
    `;

    if (orders.length === 0) {
        html += `
            <div class="products-row">
                <div class="product-cell error-message" style="grid-column: span 14;">لا توجد طلبات لعرضها.</div>
            </div>
        `;
    } else {
        orders.forEach(order => {
            const image_urls = order.image_urls ? order.image_urls.split(',') : [];
            const first_image = image_urls.length > 0 ? image_urls[0] : '../../assets/default-image.png';
            html += `
                <div class="products-row">
                    <div class="product-cell order-id">${order.id}</div>
                    <div class="product-cell product-name">
                        <img src="${first_image}" alt="${order.title}">
                        ${order.title}
                    </div>
                    <div class="product-cell price">${parseFloat(order.price).toFixed(2)} دج</div>
                    <div class="product-cell quantity">${order.product_quantity}</div>
                    <div class="product-cell color">${order.color || 'غير محدد'}</div>
                    <div class="product-cell type">${order.type || 'غير محدد'}</div>
                    <div class="product-cell diameter">${order.diameter || 'غير محدد'}</div>
                    <div class="product-cell total-price">${parseFloat(order.total_price).toFixed(2)} دج</div>
                    <div class="product-cell customer-name">${order.customer_name}</div>
                    <div class="product-cell phone">${order.customer_phone}</div>
                    <div class="product-cell address">${order.customer_address}</div>
                    <div class="product-cell order-date">${order.order_date}</div>
                    <div class="product-cell status-cell">
                        <form class="status-form" data-order-id="${order.id}">
                            <select name="status" class="${order.status}" onchange="updateStatus(this, ${order.id})">
                                <option value="confirmed" ${order.status === 'confirmed' ? 'selected' : ''}>مؤكد</option>
                                <option value="canceled" ${order.status === 'canceled' ? 'selected' : ''}>ملغى</option>
                            </select>
                        </form>
                    </div>
                    <div class="product-cell actions">
                        <a href="?delete_order_id=${order.id}" class="delete-link" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟');">حذف</a>
                    </div>
                    <div class="product-info-grid">
                        <img src="${first_image}" alt="${order.title}" class="product-image-grid">
                        <div class="product-details">
                            <h3 class="product-title">${order.title}</h3>
                            <p class="product-detail"><span>رقم الطلب:</span> ${order.id}</p>
                            <p class="product-detail"><span>السعر:</span> ${parseFloat(order.price).toFixed(2)} دج</p>
                            <p class="product-detail"><span>الكمية:</span> ${order.product_quantity}</p>
                            <p class="product-detail"><span>اللون:</span> ${order.color || 'غير محدد'}</p>
                            <p class="product-detail"><span>النوع:</span> ${order.type || 'غير محدد'}</p>
                            <p class="product-detail"><span>القطر:</span> ${order.diameter || 'غير محدد'}</p>
                            <p class="product-detail"><span>السعر الإجمالي:</span> ${parseFloat(order.total_price).toFixed(2)} دج</p>
                            <p class="product-detail"><span>اسم العميل:</span> ${order.customer_name}</p>
                            <p class="product-detail"><span>رقم الهاتف:</span> ${order.customer_phone}</p>
                            <p class="product-detail"><span>العنوان:</span> ${order.customer_address}</p>
                            <p class="product-detail"><span>تاريخ الطلب:</span> ${order.order_date}</p>
                            <p class="product-detail status" data-status="${order.status}">
                                <span>الحالة:</span> ${order.status === 'confirmed' ? 'مؤكد' : 'ملغى'}
                            </p>
                            <div class="product-actions">
                                <a href="?delete_order_id=${order.id}" class="delete-link" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب؟');">حذف</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    return html;
}

// Gestion du changement de vue entre produits et commandes
const sidebarItems = document.querySelectorAll('.sidebar-list-item');
const contentArea = document.querySelector('#content-area');
const headerText = document.querySelector('#header-text');

if (sidebarItems && contentArea && headerText) {
    let currentView = localStorage.getItem('dashboardView') || 'products';

    sidebarItems.forEach(item => {
        if (item.getAttribute('data-view') === currentView) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    productsWrapper.classList.add(currentView);
    if (currentView === 'products') {
        headerText.textContent = 'المنتجات';
        contentArea.innerHTML = generateProductsHTML(projectsData, currentView);
    } else if (currentView === 'orders') {
        headerText.textContent = 'إدارة الطلبات';
        contentArea.innerHTML = generateOrdersHTML(ordersData, currentView);
    }

    if (filterCategory && searchBar) {
        populateCategories(currentView);
        setTimeout(() => {
            applyFilters('جميع الفئات', '');
        }, 0);
    }

    sidebarItems.forEach(item => {
        item.addEventListener('click', () => {
            sidebarItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            currentView = item.getAttribute('data-view');
            productsWrapper.classList.remove('products', 'orders');
            productsWrapper.classList.add(currentView);

            localStorage.setItem('dashboardView', currentView);

            if (currentView === 'products') {
                headerText.textContent = 'المنتجات';
                contentArea.innerHTML = generateProductsHTML(projectsData, currentView);
            } else if (currentView === 'orders') {
                headerText.textContent = 'إدارة الطلبات';
                contentArea.innerHTML = generateOrdersHTML(ordersData, currentView);
            }

            if (filterCategory && searchBar) {
                populateCategories(currentView);
                filterCategory.value = 'جميع الفئات';
                currentCategory = 'جميع الفئات';
                searchBar.value = '';
                setTimeout(() => {
                    applyFilters(currentCategory, '');
                    console.log('Éléments .products-row après changement de vue :', document.querySelectorAll('.products-row').length);
                }, 0);
            }

            console.log('Vue changée à :', currentView);
        });
    });

    if (filterCategory) {
        sidebarItems.forEach(item => {
            item.addEventListener('click', () => {
                const newView = item.getAttribute('data-view');
                currentView = newView;
                populateCategories(newView);
                filterCategory.value = 'جميع الفئات';
                currentCategory = 'جميع الفئات';
                searchBar.value = '';
                setTimeout(() => {
                    applyFilters(currentCategory, '');
                }, 0);
            });
        });
    }
} else {
    console.error('Éléments .sidebar-list-item, #content-area ou #header-text non trouvés');
}

// Fonction pour mettre à jour le statut via AJAX
function updateStatus(selectElement, orderId) {
    const status = selectElement.value;
    console.log(`Mise à jour du statut pour l'ordre ${orderId} : ${status}`);

    if (status === 'canceled') {
        selectElement.classList.add('canceled');
        selectElement.classList.remove('confirmed');
        console.log('Classe "canceled" ajoutée, "confirmed" retirée');
    } else {
        selectElement.classList.add('confirmed');
        selectElement.classList.remove('canceled');
        console.log('Classe "confirmed" ajoutée, "canceled" retirée');
    }

    selectElement.style.display = 'none';
    selectElement.offsetHeight;
    selectElement.style.display = 'inline-flex';

    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('status', status);

    fetch('dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.textContent = status === 'confirmed' ? 'تم تأكيد الطلب!' : 'تم إلغاء الطلب!';
        document.querySelector('.app-content').prepend(notification);
        setTimeout(() => notification.remove(), 3000);
        console.log('Statut mis à jour avec succès');
    })
    .catch(error => {
        console.error('Erreur:', error);
        const notification = document.createElement('div');
        notification.className = 'error-notification';
        notification.textContent = '❌ حدث خطأ أثناء تحديث الحالة';
        document.querySelector('.app-content').prepend(notification);
        setTimeout(() => notification.remove(), 3000);
    });
}

// Gérer la soumission du formulaire de modification via AJAX
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Supprimer toutes les notifications existantes
    document.querySelectorAll('.success-notification').forEach(notification => {
        notification.remove();
    });

    const formData = new FormData(this);

    fetch('dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Statut de la réponse:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status} ${response.statusText}`);
        }
        return response.text();
    })
    .then(text => {
        console.log('Réponse brute:', text);
        try {
            const data = JSON.parse(text);
            console.log('Données JSON:', data);

            // Afficher uniquement la notification de succès si la mise à jour est réussie
            if (data.success) {
                const notification = document.createElement('div');
                notification.className = 'success-notification';
                notification.textContent = data.message || 'تم تحديث المنتج بنجاح';
                document.querySelector('.app-content').prepend(notification);
                setTimeout(() => notification.remove(), 5000);

                closeEditProductModal();
                // Mettre à jour projectsData
                const updatedProduct = {
                    id: parseInt(formData.get('product_id')),
                    title: formData.get('title'),
                    description: formData.get('description'),
                    category: formData.get('category'),
                    price: parseFloat(formData.get('price')),
                    color: formData.get('color'),
                    type: formData.get('type'),
                    diameter: formData.get('diameter'),
                    image_urls: data.image_urls || projectsData.find(p => p.id === parseInt(formData.get('product_id'))).image_urls
                };
                const index = projectsData.findIndex(p => p.id === updatedProduct.id);
                if (index !== -1) {
                    projectsData[index] = updatedProduct;
                }
                // Rafraîchir l'affichage
                contentArea.innerHTML = generateProductsHTML(projectsData, 'products');
                applyFilters(currentCategory, searchBar.value);
            }
        } catch (jsonError) {
            console.error('Erreur de parsing JSON:', jsonError, 'Texte brut:', text);
        }
    })
    .catch(error => {
        console.error('Erreur lors de la mise à jour du produit:', error);
    });
});



// ... Votre JavaScript existant pour le dashboard reste inchangé ...

/// Mobile Menu Functionality
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.querySelector('.hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const closeMenu = document.querySelector('.close-menu');
    const mobileNavLinks = document.querySelector('.mobile-nav-links');
  
    // Copy sidebar items to mobile menu
    const sidebarItems = document.querySelectorAll('.sidebar-list-item');
    mobileNavLinks.innerHTML = ''; // Clear existing items
    sidebarItems.forEach(item => {
        const mobileItem = document.createElement('li');
        mobileItem.setAttribute('data-view', item.getAttribute('data-view'));
        if (item.classList.contains('active')) {
            mobileItem.classList.add('active');
        }
        const link = document.createElement('a');
        link.href = '#';
        link.innerHTML = item.querySelector('a').innerHTML;
        link.addEventListener('click', function (e) {
            e.preventDefault();
            // Get the view from the clicked item
            const view = this.parentElement.getAttribute('data-view');
            
            // Update active state
            document.querySelectorAll('.sidebar-list-item, .mobile-nav-links li').forEach(el => {
                el.classList.remove('active');
                if (el.getAttribute('data-view') === view) {
                    el.classList.add('active');
                }
            });
            
            // Trigger the view change
            productsWrapper.classList.remove('products', 'orders');
            productsWrapper.classList.add(view);
            localStorage.setItem('dashboardView', view);
            
            if (view === 'products') {
                headerText.textContent = 'المنتجات';
                contentArea.innerHTML = generateProductsHTML(projectsData, view);
            } else if (view === 'orders') {
                headerText.textContent = 'إدارة الطلبات';
                contentArea.innerHTML = generateOrdersHTML(ordersData, view);
            }
            
            // Update filters
            if (filterCategory && searchBar) {
                populateCategories(view);
                filterCategory.value = 'جميع الفئات';
                currentCategory = 'جميع الفئات';
                searchBar.value = '';
                setTimeout(() => {
                    applyFilters(currentCategory, '');
                }, 0);
            }
            
            // Close mobile menu
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('active');
        });
        mobileItem.appendChild(link);
        mobileNavLinks.appendChild(mobileItem);
    });
  
    // Open/close mobile menu
    hamburger.addEventListener('click', function () {
        mobileMenu.classList.toggle('open');
        hamburger.classList.toggle('active');
    });
  
    closeMenu.addEventListener('click', function () {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('active');
    });
  
    // Close mobile menu when clicking outside
    document.addEventListener('click', function (event) {
        if (!mobileMenu.contains(event.target) && !hamburger.contains(event.target)) {
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('active');
        }
    });
});