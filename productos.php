<?php
/**
 * GREEKYA - Catálogo de Productos
 * Shop-style page with sidebar filters and grid
 */

require_once 'config/database.php';
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GREEKYA | Catálogo de Productos</title>
    <link rel="icon" href="imagen/g_oficial.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <script>
        (function () { document.body.classList.remove('light-mode'); })();
    </script>
    <!-- Navigation -->
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container split-nav">
            <!-- Left Nav -->
            <ul class="nav-left">
                <li><a href="productos.php" class="nav-link active">Productos</a></li>
                <li><a href="index.php#contacto" class="nav-link">Soporte</a></li>
            </ul>

            <!-- Center Brand -->
            <div class="nav-brand-center">
                <a href="index.php">
                    <img src="imagen/g_oficial.png" alt="GREEKYA Logo" class="brand-logo">
                    <span>GREEKYA</span>
                </a>
            </div>

            <!-- Right Nav -->
            <ul class="nav-right">
                <li><a href="index.php#acerca" class="nav-link">Sobre nosotros</a></li>
                <li><a href="garantia.php" class="nav-link">Validación de Garantía</a></li>
            </ul>

            <button class="mobile-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Shop Header -->
    <section class="shop-header">
        <div class="container">
            <h1 class="shop-title">CATÁLOGO OFICIAL</h1>
            <p class="shop-subtitle">Explora nuestra colección completa de hardware de alto rendimiento</p>
        </div>
    </section>

    <!-- Shop Container -->
    <section class="shop-section">
        <div class="container">
            <div class="shop-layout">
                <!-- Sidebar Filters -->
                <aside class="shop-sidebar">
                    <div class="filter-group">
                        <h3 class="filter-title"><i class="fas fa-filter"></i> Categorías</h3>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="checkbox" class="category-check" value="monitor" checked>
                                <span class="checkmark"></span>
                                Monitores
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" class="category-check" value="pc" checked>
                                <span class="checkmark"></span>
                                PCs / CPUs
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" class="category-check" value="peripheral" checked>
                                <span class="checkmark"></span>
                                Accesorios
                            </label>
                        </div>
                    </div>

                    <!-- Size Filters (Hidden by default, shown via JS) -->
                    <div class="filter-group" id="sizeFilters" style="display:none;">
                        <h3 class="filter-title"><i class="fas fa-expand"></i> Tamaño</h3>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="checkbox" class="size-check" value="small">
                                <span class="checkmark"></span>
                                Menos de 24"
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" class="size-check" value="24">
                                <span class="checkmark"></span>
                                24 Pulgadas
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" class="size-check" value="27">
                                <span class="checkmark"></span>
                                27 Pulgadas
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" class="size-check" value="32">
                                <span class="checkmark"></span>
                                32 Pulgadas
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" class="size-check" value="34">
                                <span class="checkmark"></span>
                                34" UltraWide
                            </label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title"><i class="fas fa-sort-amount-down"></i> Ordenar Por</h3>
                        <select id="sortSelect" class="filter-select">
                            <option value="newest">Más Recientes</option>
                            <option value="price_asc">Precio: Menor a Mayor</option>
                            <option value="price_desc">Precio: Mayor a Menor</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title"><i class="fas fa-search"></i> Buscar</h3>
                        <input type="text" id="searchInput" class="filter-input" placeholder="WCH-34, Ryzen, etc...">
                    </div>
                </aside>

                <!-- Product Grid -->
                <main class="shop-main">
                    <div id="shopGrid" class="shop-grid">
                        <!-- Products loaded via JS -->
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Cargando catálogo...
                        </div>
                    </div>

                    <!-- Pagination (Hidden initially) -->
                    <div class="pagination" id="pagination" style="display:none;">
                        <button class="page-btn prev"><i class="fas fa-chevron-left"></i></button>
                        <span class="page-info">Página 1</span>
                        <button class="page-btn next"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4><img src="imagen/g_oficial.png" alt="GREEKYA Logo" class="brand-logo"
                            style="height: 25px; vertical-align: middle; margin-right: 10px;"> GREEKYA CORE</h4>
                    <p>Ingeniería de alto nivel. Hardware de alto rendimiento diseñado para superar los límites
                        de la potencia y la percepción.</p>
                </div>

                <div class="footer-col">
                    <h4>Enlaces</h4>
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="productos.php">Productos</a></li>
                        <li><a href="index.php#garantia">Validar Garantía</a></li>
                        <li><a href="index.php#contacto">Contacto</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Redes</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 GREEKYA. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Product Details Modal -->
    <div id="productModal" class="modal-overlay">
        <div class="modal-content glass-panel">
            <button class="modal-close" id="closeModal">&times;</button>
            <div class="modal-body">
                <div class="modal-image-col">
                    <img id="modalImage" src="" alt="Product Image">
                </div>
                <div class="modal-info-col">
                    <div class="product-type-badge" id="modalTypeBadge"
                        style="position:static; display:inline-block; margin-bottom:10px;"></div>
                    <h2 id="modalTitle" class="modal-title"></h2>
                    <div id="modalSeries" class="modal-series"></div>

                    <div class="modal-price-box">
                        <span id="modalPrice" class="modal-price"></span>
                    </div>

                    <div class="modal-specs-container">
                        <h3>Especificaciones Técnicas</h3>
                        <ul id="modalSpecs" class="modal-specs-list">
                            <!-- Specs injected here -->
                        </ul>
                    </div>

                    <div class="modal-actions">
                        <a id="modalWhatsapp" href="#" target="_blank" class="btn btn-primary btn-block">
                            <i class="fab fa-whatsapp"></i> Solicitar Mayor Información
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/shop.js"></script>
    <script>
        // GREEKYA uses only Dark Mode
    </script>
</body>

</html>