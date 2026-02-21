
document.addEventListener('DOMContentLoaded', () => {
    // Initial Load
    fetchShopProducts();

    // Event Listeners
    document.querySelectorAll('.category-check').forEach(cb => {
        cb.addEventListener('change', () => {
            toggleSizeFilters();
            fetchShopProducts();
        });
    });

    document.querySelectorAll('.size-check').forEach(cb => {
        cb.addEventListener('change', fetchShopProducts);
    });

    document.getElementById('sortSelect').addEventListener('change', fetchShopProducts);

    // Debounce search
    let timeout = null;
    document.getElementById('searchInput').addEventListener('input', (e) => {
        clearTimeout(timeout);
        timeout = setTimeout(fetchShopProducts, 500);
    });

    // Modal logic
    const modal = document.getElementById('productModal');
    if (modal) {
        document.getElementById('closeModal').addEventListener('click', () => {
            modal.classList.remove('active');
            document.body.style.overflow = ''; // Restore scroll
        });

        // Close on click outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Prevent context menu on images
    document.addEventListener('contextmenu', (e) => {
        if (e.target.tagName === 'IMG') e.preventDefault();
    });

    // Initial Size Filter Check
    toggleSizeFilters();
});

let currentProducts = []; // Store fetched products locally

function toggleSizeFilters() {
    const monitorCheck = document.querySelector('.category-check[value="monitor"]');
    const sizeFilters = document.getElementById('sizeFilters');
    if (monitorCheck && monitorCheck.checked) {
        sizeFilters.style.display = 'block';
    } else {
        sizeFilters.style.display = 'none';
        // Uncheck sizes if hidden to avoid filtering by hidden inputs
        document.querySelectorAll('.size-check').forEach(cb => cb.checked = false);
    }
}

async function fetchShopProducts() {
    const grid = document.getElementById('shopGrid');
    grid.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Actualizando catálogo...</div>';

    // Build Query
    const categories = Array.from(document.querySelectorAll('.category-check:checked'))
        .map(cb => cb.value)
        .join(',');

    const sizes = Array.from(document.querySelectorAll('.size-check:checked'))
        .map(cb => cb.value)
        .join(',');

    const sort = document.getElementById('sortSelect').value;
    const search = document.getElementById('searchInput').value;

    const query = new URLSearchParams({
        categories: categories,
        sizes: sizes,
        sort: sort,
        search: search
    });

    try {
        const response = await fetch(`api/get_products.php?${query.toString()}`);
        const data = await response.json();

        if (data.success && data.products.length > 0) {
            currentProducts = data.products; // Update local store
            renderShopGrid(data.products);
        } else {
            grid.innerHTML = '<div class="no-results">No se encontraron productos con estos filtros.</div>';
        }
    } catch (error) {
        console.error('Error:', error);
        grid.innerHTML = '<div class="error">Error de conexión. Intente recargar.</div>';
    }
}

function renderShopGrid(products) {
    const grid = document.getElementById('shopGrid');
    grid.innerHTML = '';

    products.forEach((product, index) => {
        const card = document.createElement('div');
        card.className = 'product-card shop-card';
        // Add click handler to whole card
        card.onclick = (e) => {
            // Prevent if clicking whatsapp button
            if (e.target.closest('a')) return;
            openProductModal(index);
        };
        card.style.cursor = 'pointer';

        const imageHtml = product.main_image
            ? `<img src="${product.main_image}" alt="${product.model_name}">`
            : '<div class="no-image-placeholder"><i class="fas fa-desktop"></i></div>';

        const priceHtml = product.show_price && product.price
            ? `<div class="product-price">$${product.price}</div>`
            : '<div class="product-price consult-price">Consultar</div>';

        const sizeBadge = product.size
            ? `<div style="position:absolute; top:10px; left:10px; background:var(--primary); color:var(--dark-bg); padding:2px 6px; border-radius:2px; font-weight:bold; font-size:0.7rem; z-index:2;">${product.size}"</div>`
            : '';

        // Stock Badge Logic
        const stockStatus = product.stock > 0
            ? `<div class="stock-status in-stock"><i class="fas fa-check-circle"></i> Stock: ${product.stock} disp.</div>`
            : `<div class="stock-status out-of-stock"><i class="fas fa-times-circle"></i> Agotado</div>`;

        // Generate specs list
        let specsHtml = '';
        if (product.product_type === 'pc') {
            specsHtml = `
                ${product.processor ? `<li><i class="fas fa-microchip"></i> ${escapeHtml(product.processor)}</li>` : ''}
                ${product.ram ? `<li><i class="fas fa-memory"></i> ${escapeHtml(product.ram)}</li>` : ''}
                ${product.gpu ? `<li><i class="fas fa-gamepad"></i> ${escapeHtml(product.gpu)}</li>` : ''}
            `;
        } else if (product.product_type === 'peripheral') {
            specsHtml = `
                ${product.category ? `<li><i class="fas fa-keyboard"></i> ${escapeHtml(product.category.toUpperCase())}</li>` : ''}
                ${product.type ? `<li><i class="fas fa-wifi"></i> ${escapeHtml(product.type)}</li>` : ''}
                ${product.ports ? `<li><i class="fas fa-star"></i> ${escapeHtml(product.ports)}</li>` : ''}
            `;
        } else {
            specsHtml = `
                ${product.resolution ? `<li><i class="fas fa-expand-alt"></i> ${escapeHtml(product.resolution)}</li>` : ''}
                ${product.refresh_rate ? `<li><i class="fas fa-bolt"></i> ${escapeHtml(product.refresh_rate)}</li>` : ''}
                ${product.panel_type ? `<li><i class="fas fa-layer-group"></i> ${escapeHtml(product.panel_type)}</li>` : ''}
            `;
        }

        card.innerHTML = `
            <div class="product-image">
                ${sizeBadge}
                ${imageHtml}
                <div class="product-type-badge">${product.product_type.toUpperCase()}</div>
                <div style="position:absolute; bottom:10px; right:10px; background:rgba(0,0,0,0.6); color:white; padding:4px 8px; border-radius:4px; font-size:0.8rem;">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
                <div class="product-info">
                    <h3 class="product-title">${escapeHtml(product.model_name)}</h3>
                    <ul class="product-specs small-specs">
                        ${specsHtml}
                    </ul>
                    ${stockStatus}
                    ${priceHtml}
                    <a href="https://wa.me/593999791752?text=Hola, me interesa el ${encodeURIComponent(product.model_name)}"
                        class="btn btn-outline btn-block btn-sm" target="_blank">
                        <i class="fab fa-whatsapp"></i> Comprar
                    </a>
                </div>
            `;
        grid.appendChild(card);
    });
}

function openProductModal(index) {
    const product = currentProducts[index];
    if (!product) return;

    const modal = document.getElementById('productModal');

    // Populate Modal
    // Populate Modal
    const imageCol = document.querySelector('.modal-image-col');
    if (product.main_image) {
        imageCol.innerHTML = `<img id="modalImage" src="${product.main_image}" alt="${product.model_name}">`;
    } else {
        imageCol.innerHTML = `
            <div class="no-image-placeholder" style="flex-direction:column; gap:15px; width:100%;">
                <i class="fas fa-camera" style="font-size: 5rem; opacity:0.3;"></i>
                <span style="font-size:1rem; color:var(--text-muted); font-family:'Orbitron', sans-serif; letter-spacing:2px;">IMAGEN NO DISPONIBLE</span>
            </div>`;
    }
    document.getElementById('modalTitle').textContent = product.model_name;
    // document.getElementById('modalSeries').textContent = `S / N: ${ product.series_number || 'N/A' } `; // Hidden for privacy
    document.getElementById('modalTypeBadge').textContent = product.product_type.toUpperCase();

    document.getElementById('modalPrice').textContent = (product.show_price && product.price)
        ? `$${product.price} `
        : 'Consultar Precio';

    // Generate Full Specs List
    const specsList = document.getElementById('modalSpecs');
    specsList.innerHTML = ''; // Clear previous

    const specs = [
        { label: 'Procesador', val: product.processor, icon: 'microchip' },
        { label: 'Memoria RAM', val: product.ram, icon: 'memory' },
        { label: 'Almacenamiento', val: product.storage, icon: 'hdd' },
        { label: 'Gráficos (GPU)', val: product.gpu, icon: 'gamepad' },
        { label: 'Resolución', val: product.resolution, icon: 'expand-alt' },
        { label: 'Tasa Refresco', val: product.refresh_rate, icon: 'bolt' },
        { label: 'Tipo Panel', val: product.panel_type, icon: 'layer-group' },
        { label: 'Puertos', val: product.ports, icon: 'plug' },
        { label: 'Tamaño', val: product.size ? `${product.size} "` : null, icon: 'ruler-combined' },
        { label: 'Stock', val: product.stock > 0 ? `${product.stock} Unidades` : 'Agotado', icon: 'warehouse' },
        { label: 'Factor Forma', val: product.form_factor, icon: 'server' },
        { label: 'RGB', val: product.has_rgb ? 'Sí' : null, icon: 'fill-drip' },
        { label: 'Regulación', val: product.has_adjustment ? 'Sí' : null, icon: 'arrows-alt-v' }
    ];

    specs.forEach(spec => {
        if (spec.val) {
            specsList.innerHTML += `
                <li>
                    <i class="fas fa-${spec.icon}"></i>
                    <div>
                        <strong>${spec.label}:</strong> <span>${escapeHtml(spec.val)}</span>
                    </div>
                </li>
            `;
        }
    });

    // Update WhatsApp Link
    const waLink = document.getElementById('modalWhatsapp');
    waLink.href = `https://wa.me/593999791752?text=Hola, estoy interesado en el ${encodeURIComponent(product.model_name)} (${product.series_number}). Quisiera más información.`;

    // Show Modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
