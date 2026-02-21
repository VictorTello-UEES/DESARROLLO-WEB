// ============================================================
// GREEKYA - Lógica de Validación de Garantía
// ============================================================

// SELECCIÓN DE ELEMENTOS
let currentValidationData = null; // Almacena la validación activa para generar el PDF

document.addEventListener('DOMContentLoaded', () => {

    // SELECCIÓN DE ELEMENTOS
    const warrantyForm = document.getElementById('warrantySearchForm');
    const resultContainer = document.getElementById('warrantyResult');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const warrantyError = document.getElementById('warrantyError');
    const resultCard = document.querySelector('.result-card');

    if (warrantyForm) {
        // SELECCIÓN DE ELEMENTOS - Sección de búsqueda y contenedor
        const wrapper = document.getElementById('warrantyWrapper');
        const searchSection = document.querySelector('.search-section');


        // CONEXION DE EVENTOS - Captura del formulario de búsqueda
        warrantyForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Control del Comportamiento: evita recargar la página

            // SELECCIÓN DE INPUTS - Captura del valor del campo de serie
            const serialNumber = document.getElementById('serialNumber').value.trim();
            if (!serialNumber) return;

            // MANIPULACIÓN DEL DOM - UI Reset & Scanning State
            warrantyError.style.display = 'none';
            resultContainer.style.display = 'none';
            wrapper.classList.remove('has-results');

            // Mostrar spinner inline y activar animación de escaneo
            const spinner = document.getElementById('loadingSpinner');
            spinner.style.display = 'block';
            searchSection.classList.add('scanning');

            // Efecto de texto rotativo simulando escaneo del sistema
            const scanMessages = [
                'ESCANEANDO HARDWARE...',
                'VERIFICANDO BASE DE DATOS...',
                'CONSULTANDO REGISTRO DE SERIE...',
                'VALIDANDO COBERTURA...',
                'PROCESANDO RESULTADO...'
            ];
            let msgIndex = 0;
            const scanText = document.getElementById('scanTextDisplay');
            if (scanText) scanText.textContent = scanMessages[0];
            const msgInterval = setInterval(() => {
                msgIndex = (msgIndex + 1) % scanMessages.length;
                if (scanText) scanText.textContent = scanMessages[msgIndex];
            }, 400);

            // Función para detener la animación y ocultar el spinner
            function stopScanning() {
                clearInterval(msgInterval);
                spinner.style.display = 'none';
                searchSection.classList.remove('scanning');
            }

            // Timer mínimo de 1.5s para que la animación sea visible siempre
            const minDelay = new Promise(resolve => setTimeout(resolve, 45000));

            try {
                const formData = new FormData();
                formData.append('series_number', serialNumber);

                // Correr el fetch y el timer mínimo en paralelo
                const [response] = await Promise.all([
                    fetch('api/validate_warranty.php', { method: 'POST', body: formData }),
                    minDelay
                ]);

                const data = await response.json();

                stopScanning();

                if (data.success) {
                    displayResult(data);
                    wrapper.classList.add('has-results');
                } else {
                    displayError(data.message || 'No se encontró el número de serie.');
                }

            } catch (error) {
                console.error('Error validation:', error);
                // Esperar el timer mínimo antes de mostrar el error
                await minDelay;
                stopScanning();
                displayError('Error de conexión. Por favor intente más tarde.');
            }
        });
    }

    // ============================================================
    // FUNCIONES
    // ============================================================

    // Muestra los datos del producto en la tarjeta de resultados
    function displayResult(data) {
        const product = data.product;

        // Populate fields
        document.getElementById('resModel').textContent = product.model_name;
        document.getElementById('resSeries').textContent = product.series_number;

        // Product Type Labeling
        let typeLabel = 'Monitor';
        if (product.product_type === 'pc') typeLabel = 'PC';
        else if (product.product_type === 'peripheral') {
            typeLabel = product.category.charAt(0).toUpperCase() + product.category.slice(1);
        }
        document.getElementById('resType').textContent = typeLabel + (product.size ? ' de ' + product.size : '');

        document.getElementById('resPurchase').textContent = product.purchase_date;
        document.getElementById('resStatusMsg').textContent = data.message;

        // Technical Specs Logic (Monitor vs PC vs Peripheral)
        const monitorSpecs = document.getElementById('monitorSpecs');
        const pcSpecs = document.getElementById('pcSpecs');
        const peripheralSpecs = document.getElementById('peripheralSpecs');

        // Hide all first
        monitorSpecs.style.display = 'none';
        pcSpecs.style.display = 'none';
        if (peripheralSpecs) peripheralSpecs.style.display = 'none';

        if (product.product_type === 'pc') {
            pcSpecs.style.display = 'grid';
            document.getElementById('resProcessor').textContent = product.processor || 'N/A';
            document.getElementById('resRAM').textContent = product.ram || 'N/A';
            document.getElementById('resStorage').textContent = product.storage || 'N/A';
            document.getElementById('resGPU').textContent = product.gpu || 'N/A';
        } else if (product.product_type === 'peripheral') {
            if (peripheralSpecs) {
                peripheralSpecs.style.display = 'grid';
                document.getElementById('resPeripheralCat').textContent = product.category || 'N/A';
                document.getElementById('resConnectivity').textContent = product.type || 'N/A';
                document.getElementById('resRGB').textContent = product.has_rgb ? 'Sí' : 'No';
                document.getElementById('resFeatures').textContent = product.ports || 'Ver ficha técnica';
            }
        } else {
            monitorSpecs.style.display = 'grid';
            document.getElementById('resSize').textContent = product.size || 'N/A';
            document.getElementById('resResolution').textContent = product.resolution || 'N/A';
            document.getElementById('resRefresh').textContent = product.refresh_rate || 'N/A';
            document.getElementById('resPanel').textContent = product.panel_type || 'N/A';
        }

        // Status Badge
        const badge = document.getElementById('resBadge');
        badge.className = 'status-badge'; // Reset classes
        badge.textContent = product.warranty_months + ' meses';

        if (data.result === 'valid') {
            badge.classList.add('status-valid');
        } else if (data.result === 'expired') {
            badge.classList.add('status-expired');
        } else {
            badge.classList.add('status-void');
        }

        // Image (Removed from HTML to save space, keeping logic safe if re-added)
        const img = document.getElementById('resImage');
        if (img) {
            if (product.images && product.images.length > 0) {
                img.src = 'uploads/products/' + product.images[0];
            } else {
                img.src = 'imagen/logo/g_oficial.png';
            }
        }

        // Store validation data for PDF generation
        currentValidationData = data;

        // Setup download button
        const downloadBtn = document.getElementById('downloadWarrantyBtn');
        if (downloadBtn) {
            downloadBtn.onclick = () => downloadWarrantyPDF(data);
        }

        resultContainer.style.display = 'block';
    }

    // Genera y abre el PDF de validación de garantía en una nueva pestaña
    function downloadWarrantyPDF(data) {
        const downloadBtn = document.getElementById('downloadWarrantyBtn');
        const originalHTML = downloadBtn.innerHTML;

        // Show loading state
        downloadBtn.classList.add('loading');
        downloadBtn.disabled = true;
        downloadBtn.innerHTML = '<i class="fas fa-spinner"></i> Generando...';

        // Open certificate in new window using GET parameter
        const seriesNumber = encodeURIComponent(data.product.series_number);
        const url = `api/generate_warranty_pdf.php?series_number=${seriesNumber}`;
        window.open(url, '_blank');

        // Reset button after delay
        setTimeout(() => {
            downloadBtn.classList.remove('loading');
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = originalHTML;
        }, 1500);
    }

    // Función de limpieza: muestra el mensaje de error y resetea el estado de los resultados
    function displayError(message) {
        warrantyError.innerHTML = `<i class="fas fa-exclamation-triangle"></i><span>${message}</span>`;
        warrantyError.style.display = 'flex';
    }
});
