<?php
/**
 * GREEKYA - Validación de Garantía
 * Standalone page for serial number verification
 */

require_once 'config/database.php';
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garantía | GREEKYA Official</title>
    <link rel="icon" href="imagen/g_oficial.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/warranty.css">
</head>

<body>
    <script>
        (function () { document.body.classList.remove('light-mode'); })();
    </script>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container split-nav">
            <ul class="nav-left">
                <li><a href="productos.php" class="nav-link">Productos</a></li>
                <li><a href="index.php#contacto" class="nav-link">Soporte</a></li>
            </ul>

            <div class="nav-brand-center">
                <a href="index.php">
                    <img src="imagen/g_oficial.png" alt="GREEKYA Logo" class="brand-logo">
                    <span>GREEKYA</span>
                </a>
            </div>

            <ul class="nav-right">
                <li><a href="index.php#acerca" class="nav-link">Sobre nosotros</a></li>
                <li><a href="garantia.php" class="nav-link active">Validación de Garantía</a></li>
            </ul>

            <button class="mobile-toggle" id="mobileToggle">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- Warranty Header -->
    <header class="warranty-header">
        <div class="container">
            <h1 class="warranty-title">VALIDACIÓN DE GARANTÍA</h1>
            <p class="section-subtitle">Verifica el estado oficial de tu hardware GREEKYA</p>
        </div>
    </header>

    <main class="warranty-container">
        <div class="container">
            <div id="warrantyWrapper" class="warranty-layout-wrapper">
                <!-- Search Section -->
                <div class="search-section">
                    <div class="search-box-container">
                        <form id="warrantySearchForm" class="search-form">
                            <input type="text" id="serialNumber" placeholder="NÚMERO DE SERIE" class="search-input"
                                required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Validar
                            </button>
                        </form>
                        <div id="warrantyError" class="error-message-container"></div>
                    </div>

                    <!-- MENSAJE DINAMICO PARA CUANDO SE REALIZA LA CONSULTA-->
                    <div id="loadingSpinner" class="loading-spinner-inline" style="display:none;">
                        <div class="scan-bar"></div>
                        <div class="scan-text-container">
                            <span class="scan-icon"><i class="fas fa-microchip"></i></span>
                            <span id="scanTextDisplay" class="scan-label">ESCANEANDO HARDWARE...</span>
                        </div>
                        <div class="scan-dots">
                            <span></span><span></span><span></span>
                        </div>
                    </div>

                    <!-- Futuristic Decorative Elements -->
                    <div class="scanner-line"></div>
                </div>

                <!-- Results Section -->
                <div class="results-section">

                    <!-- Result -->
                    <div id="warrantyResult" class="result-container" style="display: none;">
                        <div class="result-card">
                            <div class="result-header">
                                <h3 id="resModel">MODELO DEL PRODUCTO</h3>
                                <div class="header-actions">
                                    <span id="resBadge" class="status-badge">24 MESES</span>
                                    <button id="downloadWarrantyBtn" class="btn-download"
                                        title="Descargar Validación de Garantía">
                                        <i class="fas fa-download"></i> Descargar Validación
                                    </button>
                                </div>
                            </div>
                            <div class="result-body">
                                <div class="product-details">
                                    <div class="detail-grid">
                                        <div class="detail-item">
                                            <p class="detail-label">Número de Serie</p>
                                            <p id="resSeries" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Tipo de Producto</p>
                                            <p id="resType" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Fecha de Compra</p>
                                            <p id="resPurchase" class="detail-value">-</p>
                                        </div>
                                    </div>

                                    <div class="specs-divider">ESPECIFICACIONES TÉCNICAS</div>

                                    <!-- Monitor Specs Grid -->
                                    <div id="monitorSpecs" class="detail-grid specs-grid">
                                        <div class="detail-item">
                                            <p class="detail-label">Tamaño / Dimensiones</p>
                                            <p id="resSize" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Resolución</p>
                                            <p id="resResolution" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Tasa de Refresco</p>
                                            <p id="resRefresh" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Tipo de Panel / Panel</p>
                                            <p id="resPanel" class="detail-value">-</p>
                                        </div>
                                    </div>

                                    <!-- PC Specs Grid -->
                                    <div id="pcSpecs" class="detail-grid specs-grid" style="display: none;">
                                        <div class="detail-item">
                                            <p class="detail-label">Procesador</p>
                                            <p id="resProcessor" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Memoria RAM</p>
                                            <p id="resRAM" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Almacenamiento</p>
                                            <p id="resStorage" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Gráficos (GPU)</p>
                                            <p id="resGPU" class="detail-value">-</p>
                                        </div>
                                    </div>

                                    <!-- Peripheral Specs Grid -->
                                    <div id="peripheralSpecs" class="detail-grid specs-grid" style="display: none;">
                                        <div class="detail-item">
                                            <p class="detail-label">Categoría / Línea</p>
                                            <p id="resPeripheralCat" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Conectividad</p>
                                            <p id="resConnectivity" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Iluminación</p>
                                            <p id="resRGB" class="detail-value">-</p>
                                        </div>
                                        <div class="detail-item">
                                            <p class="detail-label">Características / Puertos</p>
                                            <p id="resFeatures" class="detail-value">-</p>
                                        </div>
                                    </div>

                                    <div class="detail-item status-item">
                                        <p class="detail-label">Estado de Cobertura</p>
                                        <p id="resStatusMsg" class="detail-value">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>
                        <div class="brand-logo-container">
                            <img src="imagen/g_oficial.png" alt="GREEKYA Logo" class="brand-logo"
                                style="height: 25px; vertical-align: middle; margin-right: 10px;">
                            GREEKYA CORE
                        </div>
                    </h4>
                    <p>Ingeniería de alto nivel. Hardware de alto rendimiento diseñado para superar los límites de la
                        potencia y la percepción.</p>
                </div>

                <div class="footer-col" style="margin-left: 50px;">
                    <h4>Enlaces</h4>
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="productos.php">Productos</a></li>
                        <li><a href="garantia.php">Validar Garantía</a></li>
                        <li><a href="index.php#contacto">Contacto</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Redes</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
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

    <script src="assets/js/main.js"></script>
    <script src="assets/js/warranty.js"></script>
</body>

</html>