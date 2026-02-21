<?php
/**
 * GREEKYA - Homepage Redesigned
 * Modern design with product slider and warranty validation
 */

require_once 'config/database.php';
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GREEKYA | Sleepless Performance Monitors</title>
    <link rel="icon" href="imagen/g_oficial.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/cat_nav.css">
</head>

<body>
    <script>
        // Force Dark Mode - GREEKYA Brand Identity
        // Immediately remove light-mode if it exists or is added
        (function () {
            document.body.classList.remove('light-mode');
            // Ensure no flash of light mode by clearing any potential local storage or preference immediately for this session context if need be, 
            // but primarily relying on class removal before paint.
        })();
    </script>
    <!-- Navigation -->
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container nav-container split-nav">
            <!-- Left Nav -->
            <ul class="nav-left">
                <li><a href="productos.php" class="nav-link">Productos</a></li>
                <li><a href="#contacto" class="nav-link">Soporte</a></li>
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
                <li><a href="#acerca" class="nav-link">Sobre nosotros</a></li>
                <li><a href="garantia.php" class="nav-link">Validación de Garantía</a></li>
            </ul>

            <button class="mobile-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-background"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Tecnología de Alto Rendimiento</h1>
                <p class="hero-subtitle">Potencia bruta y precisión visual para la élite digital</p>
                <div class="hero-buttons">
                    <a href="productos.php" class="btn btn-primary">
                        <i class="fas fa-desktop"></i> Ver Productos
                    </a>
                    <a href="garantia.php" class="btn btn-outline">
                        <i class="fas fa-shield-alt"></i> Validar Garantía
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Slider Section -->
    <section class="products-section" id="productos">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Nuestros Productos</h2>
                <p class="section-subtitle">Explora nuestra gama de productos de alta calidad</p>
            </div>



            <!-- Category Navigation -->
            <div class="category-nav-container">
                <a href="productos.php?category=monitor" class="category-card">
                    <div class="category-bg">
                        <img src="imagen/cat_monitor.png" alt="Monitores Gaming">
                    </div>
                    <div class="category-content">
                        <h3>MONITORES</h3>
                        <span class="view-btn">VER SERIE <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>

                <a href="productos.php?category=pc" class="category-card">
                    <div class="category-bg">
                        <img src="imagen/cat_pc.png" alt="PCs Gaming">
                    </div>
                    <div class="category-content">
                        <h3>PC / CPU</h3>
                        <span class="view-btn">VER SERIE <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>

                <a href="productos.php?category=peripheral" class="category-card">
                    <div class="category-bg">
                        <img src="imagen/cat_peripheral.png" alt="Periféricos">
                    </div>
                    <div class="category-content">
                        <h3>PERIFÉRICOS</h3>
                        <span class="view-btn">VER SERIE <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Warranty Validation Section -->
    <section class="warranty-section" id="garantia">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Validar Garantía</h2>
                <p class="section-subtitle">Ingrese el número de serie de su producto para verificar el estado de la
                    garantía</p>
            </div>

            <div class="warranty-container">
                <div class="warranty-form-card">
                    <form id="warrantyForm" class="warranty-form">
                        <div class="form-group">
                            <label for="seriesNumber">Número de Serie</label>
                            <input type="text" id="seriesNumber" name="series_number"
                                placeholder="Ej: WCH-F340220251011001" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Validar Garantía
                        </button>
                    </form>
                </div>

                <div id="warrantyResult" class="warranty-result" style="display: none;"></div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="acerca">
        <div class="container">
            <div class="about-grid">
                <div class="about-content">
                    <h2 class="section-title">Acerca de GREEKYA</h2>
                    <p>GREEKYA nació con una visión clara: revolucionar el espacio de trabajo digital.
                        No solo creamos monitores; construimos un ecosistema de hardware de alto rendimiento
                        diseñado para superar los límites de la productividad y el gaming competitivo.</p>

                    <div class="features-grid">
                        <div class="feature-item">
                            <i class="fas fa-microchip"></i>
                            <div>
                                <h3>Ingeniería Avanzada</h3>
                                <p>Paneles de alto rendimiento con calibración de color profesional.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-halved"></i>
                            <div>
                                <h3>Protección Total</h3>
                                <p>Garantía extendida de 2 años con soporte técnico especializado.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-vr-cardboard"></i>
                            <div>
                                <h3>Inmersión Absoluta</h3>
                                <p>Diseños curvos y planos optimizados para máxima concentración.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="about-image">
                    <img src="imagen/fondo_inicio.png" alt="GREEKYA Tech">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contacto">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Enlace de Soporte</h2>
                <p class="section-subtitle">Conexión directa con nuestros especialistas</p>
            </div>

            <div class="contact-grid">
                <div class="contact-info-card">
                    <div class="contact-item">
                        <i class="fas fa-headset"></i>
                        <div>
                            <h3>CANAL DIRECTO</h3>
                            <p>+593 99 979 1752</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-at"></i>
                        <div>
                            <h3>CORE EMAIL</h3>
                            <p>info@greekya.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-location-dot"></i>
                        <div>
                            <h3>SALA DE CONTROL</h3>
                            <p>Quito, Ecuador</p>
                        </div>
                    </div>

                    <a href="https://wa.me/593999791752" target="_blank" class="btn btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> INICIAR SOPORTE WHATSAPP
                    </a>
                </div>

                <div class="contact-form-container">
                    <form id="contactForm" class="contact-form">
                        <div class="form-group">
                            <input type="text" placeholder="IDENTIFICACIÓN DE USUARIO (NOMBRE)" required>
                        </div>
                        <div class="form-group">
                            <input type="email" placeholder="ENLACE DE COMUNICACIÓN (EMAIL)" required>
                        </div>
                        <div class="form-group">
                            <textarea placeholder="TRANSMISIÓN DE MENSAJE" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-satellite-dish"></i> TRANSMITIR MENSAJE
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4><img src="imagen/g_oficial.png" alt="GREEKYA Logo" class="brand-logo"
                            style="height: 25px; vertical-align: middle; margin-right: 10px;"> GREEKYA</h4>
                    <p>Ingeniería de alto nivel. Hardware de alto rendimiento diseñado para superar los límites
                        de la potencia y la percepción.</p>
                </div>

                <div class="footer-col">
                    <h4>Enlaces</h4>
                    <ul>
                        <li><a href="#inicio">Inicio</a></li>
                        <li><a href="productos.php">Productos</a></li>
                        <li><a href="garantia.php">Validar Garantía</a></li>
                        <li><a href="#contacto">Contacto</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Distribuidores</h4>
                    <ul>
                        <li>COMPUSOFT</li>
                        <li><a href="https://www.diacia.com.ec">DIACIA DIGITAL ACCESS CIA. LTDA</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Síguenos</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 GREEKYA. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>

</html>