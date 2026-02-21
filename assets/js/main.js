// Theme Management - Locked to Dark Mode for GREEKYA
// Theme Management - Locked to Dark Mode for GREEKYA
function initTheme() {
    // 1. Eliminación inmediata
    document.body.classList.remove('light-mode');

    // 2. Prevenir cambios futuros usando MutationObserver
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                if (document.body.classList.contains('light-mode')) {
                    document.body.classList.remove('light-mode');
                    console.log('GREEKYA Security: Light mode blocked.');
                }
            }
        });
    });

    observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class']
    });
}

function toggleTheme() {
    // Desactivada para GREEKYA para mantener la identidad de marca
    console.log('Theme toggle disabled for GREEKYA');
    // Asegúrese de que permanezca oscuro incluso si lo llaman
    document.body.classList.remove('light-mode');
}

function updateThemeIcon(isLight) {
    // Mostrar siempre el icono del sol u oculto, ya que estamos en modo oscuro
    const icon = document.querySelector('.theme-toggle i');
    if (icon) {
        icon.className = 'fas fa-sun'; // O cualquier icono que represente "cambiar a luz" (que no funcionará)
    }
}

// ============================================================
// GREEKYA Frontend JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // SELECCIÓN DE ELEMENTOS
    initTheme();

    // Prevenir la descarga de imágenes
    document.addEventListener('contextmenu', (e) => {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
        }
    });

    document.addEventListener('dragstart', (e) => {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
        }
    });

    // SELECCIÓN DE ELEMENTOS - Menú móvil
    const mobileToggle = document.getElementById('mobileToggle');
    const splitNav = document.querySelector('.split-nav');

    // ESCUCHADORES - Menú móvil
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            splitNav.classList.toggle('active');
            mobileToggle.classList.toggle('active'); // Agregar animación al botón de alternancia si existe
        });
    }

    // ESCUCHADORES - Scroll suave para enlaces de navegación
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                // Cerrar el menú móvil si está abierto
                navMenu.classList.remove('active');

                // Actualizar enlace activo
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');
            }
        });
    });

    // ESCUCHADORES - Efecto de scroll en la barra de navegación
    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // SELECCIÓN DE ELEMENTOS + CARGA INICIAL - Productos
    loadProducts();

    // SELECCIÓN DE ELEMENTOS - Filtros de categoría
    const filterButtons = document.querySelectorAll('.filter-btn');
    const subFilterButtons = document.querySelectorAll('.sub-filter-btn');
    const monitorSubFilters = document.getElementById('monitorSubFilters');

    // ESCUCHADORES - Filtros principales por tipo de producto
    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Actualizar estado activo
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const type = this.dataset.type;

            // Manejar la visibilidad de los subfiltros
            if (type === 'monitor') {
                monitorSubFilters.style.display = 'flex';
                // Restablecer los subfiltros a "Todos" al cambiar a monitor
                subFilterButtons.forEach(btn => btn.classList.remove('active'));
                subFilterButtons[0].classList.add('active'); // El primero es "Todos"
                loadProducts('monitor'); // Load all monitors initially
            } else {
                monitorSubFilters.style.display = 'none';
                loadProducts(type);
            }
        });
    });

    // ESCUCHADORES - Sub-filtros por categoría de monitor
    subFilterButtons.forEach(button => {
        button.addEventListener('click', function () {
            subFilterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const category = this.dataset.category;
            // If "all" is selected in sub-filter, it means "All Monitors"
            if (category === 'all') {
                loadProducts('monitor');
            } else {
                loadProducts(category);
            }
        });
    });

    // SELECCIÓN DE ELEMENTOS - Formulario de garantía
    // CONEXION DE EVENTOS
    const warrantyForm = document.getElementById('warrantyForm');
    if (warrantyForm) {
        warrantyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            validateWarranty();
        });
    }

    // SELECCIÓN DE ELEMENTOS - Formulario de contacto - FUNCION DE LIMPIEZA
    // CONEXION DE EVENTOS
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Gracias por tu mensaje. Te contactaremos pronto.');
            this.reset();
        });
    }
});

// ============================================================
// FUNCIONES
// ============================================================

// Carga los productos desde la API según la categoría seleccionada
async function loadProducts(category = 'all') {
    const slider = document.getElementById('productSlider');
    slider.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando productos...</div>';

    try {
        const response = await fetch(`api/get_products.php?category=${category}`);
        const data = await response.json();

        if (data.success && data.products.length > 0) {
            displayProducts(data.products);
        } else {
            slider.innerHTML = '<div class="loading">No hay productos disponibles en esta categoría.</div>';
        }
    } catch (error) {
        console.error('Error loading products:', error);
        slider.innerHTML = '<div class="loading">Error al cargar productos. Por favor, intente nuevamente.</div>';
    }
}

// Muestra los productos en el slider de la página principal
function displayProducts(products) {
    const slider = document.getElementById('productSlider');
    slider.innerHTML = '';

    products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'product-card';

        const imageHtml = product.main_image
            ? `<img src="${product.main_image}" alt="${product.model_name}">`
            : '<div style="height: 100%; display: flex; align-items: center; justify-content: center; font-size: 5rem; color: var(--primary); opacity: 0.2;"><i class="fas fa-desktop"></i></div>';

        const priceHtml = product.show_price && product.price
            ? `<div class="product-price">${product.price}</div>`
            : '<div class="product-price" style="font-size: 1.2rem; color: var(--text-muted);">Consultar Precio</div>';

        card.innerHTML = `
                    <div class="product-image">
                        ${imageHtml}
                        <div style="position: absolute; top: 15px; left: 15px; background: var(--primary); color: var(--dark-bg); padding: 4px 10px; font-weight: 900; font-size: 0.75rem; border-radius: 2px; letter-spacing: 1px;">
                            ${escapeHtml(product.size)}"
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${escapeHtml(product.model_name)}</h3>
                        <ul class="product-specs">
                            ${product.product_type === 'pc' ? `
                                ${product.processor ? `<li><i class="fas fa-microchip"></i> <span>${escapeHtml(product.processor)}</span></li>` : ''}
                                ${product.ram ? `<li><i class="fas fa-memory"></i> <span>${escapeHtml(product.ram)}</span></li>` : ''}
                                ${product.storage ? `<li><i class="fas fa-hdd"></i> <span>${escapeHtml(product.storage)}</span></li>` : ''}
                                ${product.gpu ? `<li><i class="fas fa-gamepad"></i> <span>${escapeHtml(product.gpu)}</span></li>` : ''}
                            ` : `
                                ${product.resolution ? `<li><i class="fas fa-expand-alt"></i> <span>${escapeHtml(product.resolution)}</span></li>` : ''}
                                ${product.refresh_rate ? `<li><i class="fas fa-bolt"></i> <span>${escapeHtml(product.refresh_rate)}</span></li>` : ''}
                                ${product.panel_type ? `<li><i class="fas fa-layer-group"></i> <span>Panel ${escapeHtml(product.panel_type)}</span></li>` : ''}
                                ${product.has_rgb ? `<li><i class="fas fa-fill-drip"></i> <span>RGB SYNC</span></li>` : ''}
                            `}
                        </ul>
                        <div class="stock-status ${product.stock > 0 ? 'in-stock' : 'out-of-stock'}">
                            <i class="fas fa-${product.stock > 0 ? 'check-circle' : 'times-circle'}"></i>
                            ${product.stock > 0 ? `STOCK: ${product.stock} DISP.` : 'AGOTADO'}
                        </div>
                        ${priceHtml}
                        <a href="https://wa.me/593999791752?text=Hola, me interesa el ${encodeURIComponent(product.model_name)} (${encodeURIComponent(product.series_number)})" 
                        class="btn btn-outline btn-block" target="_blank" style="margin-top: auto;">
                            <i class="fab fa-whatsapp"></i> Solicitar Info
                        </a>
                    </div>
                `;

        slider.appendChild(card);
    });
}

// Valida la garantía de un producto mediante su número de serie
async function validateWarranty() {
    const seriesNumber = document.getElementById('seriesNumber').value.trim();
    const resultDiv = document.getElementById('warrantyResult');

    if (!seriesNumber) {
        showWarrantyResult('error', 'Por favor ingrese un número de serie.');
        return;
    }
    // Manipulacion del DOM - MENSAJE DINAMICO DE CARGA
    resultDiv.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> ESCANEANDO SISTEMA...</div>';
    resultDiv.style.display = 'block';

    try {
        const formData = new FormData();
        formData.append('series_number', seriesNumber);

        const response = await fetch('api/validate_warranty.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            const product = data.product;
            let resultClass = 'success';
            let icon = 'check-circle';
            if (data.result === 'expired') { resultClass = 'warning'; icon = 'exclamation-triangle'; }
            if (data.result === 'void') { resultClass = 'error'; icon = 'times-circle'; }

            const html = `
                <div class="warranty-result ${resultClass}" style="padding: 40px; border-radius: 4px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.3);">
                    <h3 style="margin-bottom: 25px; display: flex; align-items: center; gap: 15px; color: var(--${resultClass === 'success' ? 'success' : resultClass === 'warning' ? 'warning' : 'danger'});">
                        <i class="fas fa-${icon}" style="font-size: 2rem;"></i>
                        <span style="font-family: 'Orbitron', sans-serif;">${escapeHtml(data.message.toUpperCase())}</span>
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left;">
                        <div style="border-left: 2px solid var(--glass-border); padding-left: 15px;">
                            <p style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">Modelo</p>
                            <p style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem;">${escapeHtml(product.model_name)}</p>
                        </div>
                        <div style="border-left: 2px solid var(--glass-border); padding-left: 15px;">
                            <p style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">Tipo</p>
                             <p style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem; text-transform:uppercase;">
                                ${product.product_type === 'pc' ? 'PC GAMING / CPU' :
                    product.product_type === 'peripheral' ? 'ACCESORIO' : 'MONITOR'}
                            </p>
                        </div>
                        <div style="border-left: 2px solid var(--glass-border); padding-left: 15px;">
                            <p style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">S/N</p>
                            <p style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem;">${escapeHtml(product.series_number)}</p>
                        </div>
                        <div style="border-left: 2px solid var(--glass-border); padding-left: 15px;">
                            <p style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">Compra</p>
                            <p style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem;">${escapeHtml(product.purchase_date)}</p>
                        </div>
                        <div style="border-left: 2px solid var(--glass-border); padding-left: 15px;">
                            <p style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">Estado</p>
                            <p style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem; color: var(--${resultClass === 'success' ? 'success' : resultClass === 'warning' ? 'warning' : 'danger'});">
                                ${data.result === 'valid' ? 'ACTIVA' : 'INACTIVA/EXPIRADA'}
                            </p>
                        </div>
                        <div style="border-left: 2px solid var(--glass-border); padding-left: 15px;">
                            <p style="text-transform: uppercase; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">Garantía</p>
                            <p style="font-family: 'Orbitron', sans-serif; font-size: 1.1rem;">
                                ${escapeHtml(product.warranty_months)} Meses
                            </p>
                        </div>
                    </div>
                </div>
            `;
            resultDiv.innerHTML = html;
        } else {
            showWarrantyResult('error', data.message);
        }
    } catch (error) {
        console.error('Error validating warranty:', error);
        showWarrantyResult('error', 'SYSTEM ERROR: NO SE PUDO VALIDAR.');
    }
}

// Muestra el resultado de la validación de garantía en la interfaz
function showWarrantyResult(type, message) {
    const resultDiv = document.getElementById('warrantyResult');
    const icon = type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-circle' : 'times-circle';

    resultDiv.innerHTML = `
        <div class="warranty-result ${type}">
            <h3 style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-${icon}"></i>
                ${escapeHtml(message)}
            </h3>
        </div>
    `;
    resultDiv.style.display = 'block';
}

// Función de limpieza: escapa caracteres HTML para prevenir XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ============================================================
// ESCUCHADORES - Controles del slider de productos
// ============================================================
const sliderPrev = document.querySelector('.slider-prev');
const sliderNext = document.querySelector('.slider-next');

if (sliderPrev && sliderNext) {
    sliderPrev.addEventListener('click', function () {
        const slider = document.getElementById('productSlider');
        slider.scrollBy({ left: -440, behavior: 'smooth' });
    });

    sliderNext.addEventListener('click', function () {
        const slider = document.getElementById('productSlider');
        slider.scrollBy({ left: 440, behavior: 'smooth' });
    });
}
