# Estructura del Sitio Web - Monitores Gamer GREEKYA

Este documento describe la estructura actualizada y las características técnicas del proyecto web "GREEKYA".

## Estructura de Archivos

El proyecto contiene los siguientes archivos:

-   `style.css`: Hoja de estilos principal (Diseño Responsive, Variables CSS).
-   `index.html`: Página de inicio con sección "Hero" y accesos rápidos.
-   `productos.html`: Catálogo de productos con diseño en Grid.
-   `servicios.html`: (**Nuevo**) Página dedicada a los servicios ofrecidos.
-   `validacion_garantia.html`: Formulario de validación de garantía.
-   `nosotros.html`: Información sobre la empresa (Misión/Visión).
-   `contacto.html`: Formulario de contacto y datos de ubicación.

---

## 👨💻 Equipo de desarrollo

-   **David Tello**
    -   _Lider de Proyecto_
    -   _Desarrollo_
    -   _Backend_
    -   _Frontend_
    -   _Documentación_

---

## ⚙️ Tecnologías utilizadas

-   **Frontend**: HTML5, CSS3
-   **Diseño**: Flexbox, CSS Grid, Responsive Design (Media Queries)
-   **Iconografía**: Font Awesome 6.4.0
-   **Tipografía**: Google Fonts (Poppins & Roboto)
-   **Control de versiones**: Git

## Características Implementadas

### Estilos (CSS3)
-   **Variables CSS**: Uso de `:root` para gestionar la paleta de colores institucional (Azul Oscuro `#1a237e`, Acento Cyan `#00bcd4`).
-   **Modo Oscuro Parcial**: Encabezado y Pie de página con fondo `#050a14` para mayor contraste y estética "Gaming".
-   **Layouts Modernos**: 
    -   `Flexbox` para la navegación y alineación de elementos.
    -   `CSS Grid` para las tarjetas de productos y servicios (`.grid-container`).
-   **Hero Section**: Imagen de fondo con superposición de gradiente y efecto visual impactante en la página de inicio.
-   **Responsive Design**: Adaptación a móviles y tablets mediante `@media queries`.

### HTML5 y Semántica
-   **Etiquetas Semánticas**: Uso correcto de `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`.
-   ** Navegación Mejorada**: Implementación de enlaces `<a>` estilizados como botones para una correcta navegación entre páginas.
-   **Formularios Estilizados**: Entradas de texto y botones con estados `focus` y `hover` visuales.
-   **Iconos Sociales**: Integración de redes sociales en el pie de página con clases dedicadas.

## Verificación

Se ha verificado la funcionalidad completa del sitio:

### Pasos para probar
1.  Abrir `index.html`.
2.  Verificar que la sección "Hero" muestra la imagen de fondo correctamente.
3.  Navegar a "Productos" y probar el botón "Ver Detalles" (debe redirigir correctamente).
4.  Navegar a "Servicios" y verificar la cuadrícula de iconos.
5.  Comprobar que el pie de página muestra los iconos de redes sociales en todas las páginas.

## 📝 Licencia

Este proyecto fue desarrollado con fines académicos para la asignatura de Desarrollo de Aplicaciones Web. Puedes utilizarlo como referencia para proyectos educativos.

---

## 📌 Notas

-   Este repositorio contiene la versión estable del frontend.
-   Se ha optimizado la velocidad de carga utilizando fuentes e iconos vía CDN.
