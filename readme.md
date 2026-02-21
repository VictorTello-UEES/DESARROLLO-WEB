# GREEKYA — Sitio Web Oficial

> **Sleepless Performance Monitors** | Hardware de alto rendimiento para gaming y productividad profesional.

---

## 📋 Descripción del Proyecto

GREEKYA es una plataforma web completa para la presentación, catálogo y soporte postventa de monitores y periféricos gaming. El proyecto ha evolucionado desde un frontend estático hasta un sistema **full-stack** con backend PHP, base de datos MySQL, panel de administración y APIs REST propias.

---

## 🗂️ Estructura de Archivos

```
MONITORES_3/
│
├── index.php                   # Página de inicio (Hero, Categorías, Garantía, Contacto)
├── productos.php               # Catálogo de productos con filtros por categoría
├── garantia.php                # Página de validación de garantía del cliente
│
├── api/                        # APIs REST del sistema
│   ├── validate_warranty.php   # Validación de garantía por número de serie
│   ├── generate_warranty_pdf.php # Generación de certificado de garantía en PDF
│   └── get_products.php        # Endpoint de consulta de productos
│
├── assets/
│   ├── css/
│   │   ├── style.css           # Hoja de estilos principal (Dark Mode, Variables CSS)
│   │   ├── warranty.css        # Estilos específicos de la garantía
│   │   └── cat_nav.css         # Estilos del navegador de categorías
│   └── js/
│       ├── main.js             # Lógica general: navbar, garantía, contacto, slider
│       ├── shop.js             # Lógica del catálogo: filtros, búsqueda, paginación
│       └── warranty.js         # Lógica SPA de validación de garantía
│
│
└── imagen/                     # Recursos gráficos estáticos del sitio
```

---

## 👨‍💻 Equipo de Desarrollo

| Rol | Nombre |
|---|---|
| Líder de Proyecto | **David Tello** |
| Desarrollo Backend | **David Tello** |
| Desarrollo Frontend | **David Tello** |
| Documentación | **David Tello** |

---

## ⚙️ Tecnologías Utilizadas

### Backend
- **PHP 8+** — Lógica de servidor, APIs REST, autenticación de administradores
- **MySQL / MariaDB** — Base de datos relacional (vía XAMPP) - IMPLEMENTACION FUTURA
- **PDO** — Capa de abstracción de base de datos con consultas preparadas
- **mPDF / TCPDF** — Generación de certificados de garantía en PDF - IMPLEMENTACION FUTURA

### Frontend
- **HTML5** — Estructura semántica (`<header>`, `<nav>`, `<main>`, `<section>`, `<footer>`)
- **CSS3** — Variables CSS, Flexbox, CSS Grid, Media Queries, Dark Mode
- **JavaScript (Vanilla ES6+)** — Lógica de UI, fetch API, animaciones, formularios

### Herramientas y Librerías
- **Font Awesome 6.4.0** — Iconografía (vía CDN)
- **Google Fonts** — Tipografías: `Orbitron` y `Rajdhani`
- **Git** — Control de versiones


---

## 🌟 Características Implementadas

### Frontend
- **Dark Mode forzado** — Identidad visual gaming oscura, aplicada inmediatamente al cargar para evitar flash
- **Navbar dividida (Split Nav)** — Logo centrado con enlaces a izquierda y derecha; versión hamburguesa para móvil
- **Navegador de Categorías** — Tarjetas visuales con imagen de fondo (Monitores, PC/CPU, Periféricos)
- **Validación de Garantía integrada** — Widget en la homepage; página dedicada `garantia.php` con resultado dinámico
- **Catálogo con filtros** — Búsqueda y filtro por categoría en `productos.php`, sin recarga de página
- **Sección de Contacto** — Enlace rápido a WhatsApp, formulario de mensaje integrado
- **Tipografías Gaming** — `Orbitron` (títulos) y `Rajdhani` (cuerpo) vía Google Fonts

---

## 🚀 Instalación Local (XAMPP)

1. Copiar la carpeta del proyecto en `c:/xampp/htdocs/MONITORES_3/`
2. Iniciar **Apache** y **MySQL** desde el Panel de Control de XAMPP
---

## 📝 Licencia

Este proyecto fue desarrollado con fines académicos y comerciales para la marca **GREEKYA**. Todos los derechos sobre el diseño, contenido y código pertenecen al equipo de desarrollo.

