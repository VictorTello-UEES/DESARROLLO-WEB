<?php
/**
 * GREEKYA Warranty Certificate Generator
 * Certificado completo de 2 páginas con términos y condiciones
 */

require_once '../config/database.php';
require_once '../config/config.php';

// Obtener el número de serie de GET o POST
$seriesNumber = '';
if (isset($_GET['series_number'])) {
    $seriesNumber = sanitizeInput($_GET['series_number']);
} elseif (isset($_POST['series_number'])) {
    $seriesNumber = sanitizeInput($_POST['series_number']);
}

if (empty($seriesNumber)) {
    die('Error: Número de serie no proporcionado.');
}

try {
    // Obtener datos del producto
    $sql = "SELECT * FROM products WHERE series_number = :series_number LIMIT 1";
    $product = fetchOne($sql, ['series_number' => $seriesNumber]);

    if (!$product) {
        die('Error: Producto no encontrado.');
    }

    // Verificar estado de garantía
    $warrantyStatus = 'valid';
    $warrantyMessage = 'GARANTÍA VIGENTE';
    $statusColor = '#00AA5B'; // Verde

    if ($product['status'] === 'void') {
        $warrantyStatus = 'void';
        $warrantyMessage = 'GARANTÍA ANULADA';
        $statusColor = '#FF8C00'; // Orange
    } elseif ($product['status'] === 'expired') {
        $warrantyStatus = 'expired';
        $warrantyMessage = 'GARANTÍA EXPIRADA';
        $statusColor = '#DC3545'; // Red
    } elseif ($product['purchase_date']) {
        $expirationDate = getWarrantyExpiration($product['purchase_date'], $product['warranty_months']);
        if (!isWarrantyValid($product['purchase_date'], $product['warranty_months'])) {
            $warrantyStatus = 'expired';
            $warrantyMessage = 'GARANTÍA EXPIRADA';
            $statusColor = '#DC3545';
        }
    }

    // Generate HTML certificate
    generateCertificate($product, $warrantyStatus, $warrantyMessage, $statusColor);

} catch (Exception $e) {
    error_log("Certificate Generation Error: " . $e->getMessage());
    die('Error al generar el certificado. Por favor intente nuevamente.');
}

function generateCertificate($product, $status, $message, $statusColor)
{
    $currentDate = date('d/m/Y H:i:s');

    $productType = 'Monitor';
    if ($product['product_type'] === 'pc') {
        $productType = 'PC Gaming';
    } elseif ($product['product_type'] === 'peripheral') {
        $productType = ucfirst($product['category']);
    }

    // Calcular fecha de expiración si corresponde
    $expirationInfo = '';
    if ($product['purchase_date']) {
        $expirationDate = getWarrantyExpiration($product['purchase_date'], $product['warranty_months']);
        if ($status === 'valid') {
            $expirationInfo = 'Válida hasta: ' . formatDate($expirationDate);
        } elseif ($status === 'expired') {
            $expirationInfo = 'Expiró el: ' . formatDate($expirationDate);
        }
    }

    // Salida del certificado HTML
    header('Content-Type: text/html; charset=UTF-8');
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Certificado de Garantía - <?php echo htmlspecialchars($product['series_number']); ?></title>
        <style>
            @page {
                size: A4;
                margin: 15mm;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', 'Arial', sans-serif;
                color: #1a1a1a;
                line-height: 1.4;
                background: #f5f5f5;
                padding: 10px;
            }

            .certificate-container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
                position: relative;
                padding: 25px 35px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                page-break-after: always;
            }

            .terms-container {
                max-width: 210mm;
                margin: 20px auto 0;
                background: white;
                position: relative;
                padding: 25px 35px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            }

            /* Futuristic corner designs */
            .corner {
                position: absolute;
                width: 60px;
                height: 60px;
                border: 2px solid #00f2ff;
            }

            .corner::before,
            .corner::after {
                content: '';
                position: absolute;
                background: #00f2ff;
            }

            .corner-top-left {
                top: 15px;
                left: 15px;
                border-right: none;
                border-bottom: none;
                border-top-left-radius: 3px;
            }

            .corner-top-left::before {
                width: 20px;
                height: 2px;
                top: -2px;
                left: -2px;
            }

            .corner-top-left::after {
                width: 2px;
                height: 20px;
                top: -2px;
                left: -2px;
            }

            .corner-top-right {
                top: 15px;
                right: 15px;
                border-left: none;
                border-bottom: none;
                border-top-right-radius: 3px;
            }

            .corner-top-right::before {
                width: 20px;
                height: 2px;
                top: -2px;
                right: -2px;
            }

            .corner-top-right::after {
                width: 2px;
                height: 20px;
                top: -2px;
                right: -2px;
            }

            .corner-bottom-left {
                bottom: 15px;
                left: 15px;
                border-right: none;
                border-top: none;
                border-bottom-left-radius: 3px;
            }

            .corner-bottom-left::before {
                width: 20px;
                height: 2px;
                bottom: -2px;
                left: -2px;
            }

            .corner-bottom-left::after {
                width: 2px;
                height: 20px;
                bottom: -2px;
                left: -2px;
            }

            .corner-bottom-right {
                bottom: 15px;
                right: 15px;
                border-left: none;
                border-top: none;
                border-bottom-right-radius: 3px;
            }

            .corner-bottom-right::before {
                width: 20px;
                height: 2px;
                bottom: -2px;
                right: -2px;
            }

            .corner-bottom-right::after {
                width: 2px;
                height: 20px;
                bottom: -2px;
                right: -2px;
            }

            /* Header with logo */
            .header {
                text-align: center;
                margin-bottom: 20px;
                position: relative;
                z-index: 10;
            }

            .logo {
                max-width: 200px;
                height: auto;
            }

            /* Certificate title */
            .certificate-title {
                text-align: center;
                font-size: 22px;
                font-weight: bold;
                color: #1a1a1a;
                margin: 15px 0;
                letter-spacing: 3px;
                text-transform: uppercase;
                position: relative;
                z-index: 10;
            }

            .title-line {
                height: 2px;
                background: linear-gradient(90deg, transparent, #00f2ff, transparent);
                margin: 10px auto;
                width: 80%;
            }

            /* Status badge */
            .status-section {
                text-align: center;
                margin: 15px 0;
                position: relative;
                z-index: 10;
            }

            .status-badge {
                display: inline-block;
                padding: 10px 30px;
                background: white;
                border: 2px solid
                    <?php echo $statusColor; ?>
                ;
                color:
                    <?php echo $statusColor; ?>
                ;
                font-size: 16px;
                font-weight: bold;
                border-radius: 3px;
                letter-spacing: 2px;
            }

            .status-info {
                margin-top: 5px;
                font-size: 11px;
                color: #64748b;
                font-style: italic;
            }

            /* Product information */
            .info-section {
                margin: 15px 0;
                position: relative;
                z-index: 10;
            }

            .section-title {
                font-size: 14px;
                font-weight: bold;
                color: #1a1a1a;
                margin-bottom: 10px;
                padding-bottom: 5px;
                border-bottom: 2px solid #00f2ff;
                text-transform: uppercase;
                letter-spacing: 1.5px;
            }

            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 15px;
            }

            .info-item {
                padding: 10px;
                background: #f8fafc;
                border-left: 3px solid #00f2ff;
                border-radius: 2px;
            }

            .info-label {
                font-size: 9px;
                font-weight: bold;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 3px;
            }

            .info-value {
                font-size: 13px;
                color: #1a1a1a;
                font-weight: 600;
            }

            /* Specifications */
            .specs-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .spec-item {
                padding: 8px;
                background: #f8fafc;
                border-radius: 2px;
                border: 1px solid #e2e8f0;
                border-left: 2px solid #00f2ff;
            }

            /* Footer */
            .footer {
                margin-top: 20px;
                padding-top: 10px;
                border-top: 1px solid #e2e8f0;
                text-align: center;
                font-size: 9px;
                color: #64748b;
                position: relative;
                z-index: 10;
            }

            .footer p {
                margin: 3px 0;
            }

            .validation-date {
                text-align: right;
                font-size: 8px;
                color: #94a3b8;
                margin-top: 10px;
                font-style: italic;
            }

            /* Terms and Conditions Styles */
            .terms-title {
                font-size: 18px;
                font-weight: bold;
                color: #1a1a1a;
                margin-bottom: 15px;
                padding-bottom: 8px;
                border-bottom: 2px solid #00f2ff;
                text-transform: uppercase;
                letter-spacing: 2px;
            }

            .terms-section {
                margin-bottom: 15px;
            }

            .terms-subtitle {
                font-size: 12px;
                font-weight: bold;
                color: #1a1a1a;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .terms-text {
                font-size: 10px;
                color: #334155;
                line-height: 1.5;
                margin-bottom: 8px;
                text-align: justify;
            }

            .terms-list {
                margin-left: 20px;
                margin-bottom: 10px;
            }

            .terms-list li {
                font-size: 10px;
                color: #334155;
                line-height: 1.6;
                margin-bottom: 4px;
            }

            .warranty-periods {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin: 10px 0;
            }

            .warranty-period-item {
                padding: 8px;
                background: #f8fafc;
                border-left: 3px solid #00f2ff;
                border-radius: 2px;
            }

            .warranty-period-label {
                font-size: 9px;
                font-weight: bold;
                color: #64748b;
                text-transform: uppercase;
            }

            .warranty-period-value {
                font-size: 11px;
                color: #1a1a1a;
                font-weight: 600;
            }

            .contact-info {
                background: #f0f9ff;
                padding: 12px;
                border-left: 3px solid #00f2ff;
                border-radius: 2px;
                margin: 10px 0;
            }

            .contact-info p {
                font-size: 10px;
                color: #1e293b;
                margin: 3px 0;
            }

            /* Print button */
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 25px;
                background: #00f2ff;
                color: #0a0a0a;
                border: none;
                border-radius: 3px;
                font-weight: bold;
                cursor: pointer;
                font-size: 13px;
                box-shadow: 0 4px 15px rgba(0, 242, 255, 0.3);
                z-index: 1000;
                transition: all 0.3s;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .print-button:hover {
                background: #00d4e0;
                box-shadow: 0 6px 20px rgba(0, 242, 255, 0.5);
                transform: translateY(-2px);
            }

            @media print {
                body {
                    padding: 0;
                    background: white;
                }

                .certificate-container,
                .terms-container {
                    margin: 0;
                    padding: 20mm 15mm;
                    box-shadow: none;
                }

                .certificate-container {
                    page-break-after: always;
                }

                .no-print {
                    display: none !important;
                }
            }
        </style>
    </head>

    <body>
        <button class="print-button no-print" onclick="window.print()">
            🖨️ Imprimir PDF
        </button>

        <!-- PAGE 1: Certificate -->
        <div class="certificate-container">
            <!-- Futuristic corners -->
            <div class="corner corner-top-left"></div>
            <div class="corner corner-top-right"></div>
            <div class="corner corner-bottom-left"></div>
            <div class="corner corner-bottom-right"></div>

            <!-- Header with logo -->
            <div class="header">
                <img src="../imagen/greekya_sin_fondo.png" alt="GREEKYA" class="logo">
            </div>

            <!-- Certificate title -->
            <h1 class="certificate-title">Certificado de Garantía</h1>
            <div class="title-line"></div>

            <!-- Status section -->
            <div class="status-section">
                <div class="status-badge"><?php echo $message; ?></div>
                <?php if ($expirationInfo): ?>
                    <div class="status-info"><?php echo $expirationInfo; ?></div>
                <?php endif; ?>
            </div>

            <!-- Product information -->
            <div class="info-section">
                <h2 class="section-title">Información del Producto</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Modelo</div>
                        <div class="info-value"><?php echo htmlspecialchars($product['model_name']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Número de Serie</div>
                        <div class="info-value"><?php echo htmlspecialchars($product['series_number']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tipo</div>
                        <div class="info-value">
                            <?php echo $productType . ($product['size'] ? ' - ' . $product['size'] : ''); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Garantía</div>
                        <div class="info-value"><?php echo $product['warranty_months']; ?> meses</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Fecha de Compra</div>
                        <div class="info-value">
                            <?php echo $product['purchase_date'] ? formatDate($product['purchase_date']) : 'No registrada'; ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Estado</div>
                        <div class="info-value" style="color: <?php echo $statusColor; ?>;">
                            <?php echo $status === 'valid' ? 'ACTIVA' : ($status === 'expired' ? 'EXPIRADA' : 'ANULADA'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical specifications -->
            <div class="info-section">
                <h2 class="section-title">Especificaciones Técnicas</h2>
                <div class="specs-grid">
                    <?php if ($product['product_type'] === 'pc'): ?>
                        <div class="spec-item">
                            <div class="info-label">Procesador</div>
                            <div class="info-value"><?php echo $product['processor'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">RAM</div>
                            <div class="info-value"><?php echo $product['ram'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">Almacenamiento</div>
                            <div class="info-value"><?php echo $product['storage'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">GPU</div>
                            <div class="info-value"><?php echo $product['gpu'] ?? 'N/A'; ?></div>
                        </div>
                    <?php elseif ($product['product_type'] === 'peripheral'): ?>
                        <div class="spec-item">
                            <div class="info-label">Categoría</div>
                            <div class="info-value"><?php echo ucfirst($product['category']) ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">Conectividad</div>
                            <div class="info-value"><?php echo $product['type'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">RGB</div>
                            <div class="info-value"><?php echo ($product['has_rgb'] ?? 0) ? 'Sí' : 'No'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">Características</div>
                            <div class="info-value"><?php echo $product['ports'] ?? 'Ver ficha'; ?></div>
                        </div>
                    <?php else: ?>
                        <div class="spec-item">
                            <div class="info-label">Tamaño</div>
                            <div class="info-value"><?php echo $product['size'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">Resolución</div>
                            <div class="info-value"><?php echo $product['resolution'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">Refresco</div>
                            <div class="info-value"><?php echo $product['refresh_rate'] ?? 'N/A'; ?></div>
                        </div>
                        <div class="spec-item">
                            <div class="info-label">Panel</div>
                            <div class="info-value"><?php echo $product['panel_type'] ?? 'N/A'; ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Validation date -->
            <div class="validation-date">
                Generado: <?php echo $currentDate; ?>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><strong>GREEKYA</strong> - Hardware Oficial de Alto Rendimiento</p>
                <p>Este certificado valida la garantía del producto especificado</p>
                <p>www.greekya.com | soporte@greekya.com</p>
            </div>
        </div>

        <!-- PAGE 2: Terms and Conditions -->
        <div class="terms-container">
            <!-- Futuristic corners -->
            <div class="corner corner-top-left"></div>
            <div class="corner corner-top-right"></div>
            <div class="corner corner-bottom-left"></div>
            <div class="corner corner-bottom-right"></div>

            <h1 class="terms-title">Términos y Condiciones de Garantía</h1>

            <!-- General Conditions -->
            <div class="terms-section">
                <h2 class="terms-subtitle">Condiciones Generales</h2>
                <p class="terms-text">
                    Todos los productos GREEKYA cuentan con garantía oficial contra defectos de fabricación a partir de la
                    fecha de
                    adquisición según el siguiente cuadro:
                </p>

                <div class="warranty-periods">
                    <div class="warranty-period-item">
                        <div class="warranty-period-label">Monitores</div>
                        <div class="warranty-period-value">24 Meses</div>
                    </div>
                    <div class="warranty-period-item">
                        <div class="warranty-period-label">PC Gaming / Desktop</div>
                        <div class="warranty-period-value">24 Meses</div>
                    </div>
                    <div class="warranty-period-item">
                        <div class="warranty-period-label">Periféricos</div>
                        <div class="warranty-period-value">12 Meses</div>
                    </div>
                    <div class="warranty-period-item">
                        <div class="warranty-period-label">Accesorios</div>
                        <div class="warranty-period-value">6 Meses</div>
                    </div>
                </div>

                <div class="contact-info">
                    <p><strong>Para hacer efectiva la garantía:</strong></p>
                    <p>📞 Contacte a nuestro número telefónico o consulte nuestro sitio web</p>
                    <p>🌐 www.greekya.com | ✉️ soporte@greekya.com</p>
                </div>

                <p class="terms-text">
                    • La garantía en todos los equipos GREEKYA no cubre la pérdida de información del usuario.<br>
                    • GREEKYA ni sus Centros Autorizados de Servicio serán responsables por ningún tipo de pérdida de
                    información
                    que se produzca por la utilización y/o falla de los productos electrónicos de nuestros equipos.
                </p>
            </div>

            <!-- Requirements -->
            <div class="terms-section">
                <h2 class="terms-subtitle">Requisitos para Reclamos de Garantía</h2>
                <ul class="terms-list">
                    <li><strong>a.</strong> Presentar factura original o certificado de garantía</li>
                    <li><strong>b.</strong> Equipo con series perfectamente legibles (número de serie visible)</li>
                    <li><strong>c.</strong> Equipo sin daños físicos externos</li>
                    <li><strong>d.</strong> Equipo en perfecto estado físico (sin golpes, roturas o manipulación no
                        autorizada)</li>
                    <li><strong>e.</strong> Realizar mantenimiento preventivo mínimo 1 vez al año por personal autorizado
                        GREEKYA</li>
                </ul>

                <p class="terms-text">
                    <strong>Importante:</strong> No se podrán realizar reclamos de garantía en equipos que no hayan sido
                    vendidos o
                    autorizados por GREEKYA. La gestión de la garantía se realizará en nuestros centros autorizados de
                    servicio y no
                    tendrá costo alguno tanto por el hardware o software instalado en nuestros equipos.
                </p>

                <p class="terms-text">
                    El tiempo de ejecución de nuestra garantía es de 96 horas máximo después de haber recibido el producto
                    en nuestro
                    Centro de Servicio Autorizado.
                </p>
            </div>

            <!-- Exclusions -->
            <div class="terms-section">
                <h2 class="terms-subtitle">La Garantía NO Cubre lo Siguiente:</h2>

                <p class="terms-text"><strong>Daños Físicos:</strong></p>
                <ul class="terms-list">
                    <li>Golpes, daños estéticos y cosméticos como roturas, rayones, pintura deteriorada, producto de una
                        inadecuada
                        manipulación del usuario o exposición a factores ambientales extremos</li>
                    <li>Fallas de alimentación de energía eléctrica intermitente, sobretensión de energía, ausencia de un
                        adecuado
                        sistema de puesta a tierra, o cualquier otro motivo que no sea atribuible a un desperfecto de
                        fabricación</li>
                    <li>Violación o remoción de sellos de seguridad y de etiquetas con señales que permitan la
                        identificación del producto</li>
                </ul>

                <p class="terms-text"><strong>Daños de Software:</strong></p>
                <ul class="terms-list">
                    <li>Mal funcionamiento, error o falla del equipo provocada por la instalación de software pirata</li>
                    <li>Instalación de software no compatible con las especificaciones del equipo</li>
                    <li>Producto por Virus, Malware, Jokes, Browser Hijackers, SPAM, así como cualquier software malicioso a
                        través del
                        uso del equipo</li>
                    <li>Desconfiguración de software que causen fallas al equipo o dañen parcial o permanentemente alguna
                        parte del
                        hardware o del contenido original del equipo</li>
                </ul>

                <p class="terms-text">
                    La garantía en el software solo cubre la reinstalación del software original del equipo, mismos que haya
                    sido
                    dañados por defecto de fábrica. En caso de necesitar una recuperación de información, archivos del
                    sistema del
                    cliente, tendrá un costo adicional que contenga éste.
                </p>

                <p class="terms-text">
                    Las solicitudes de garantías de equipos GREEKYA se ejecutan con partes y piezas iguales o mejores
                    características,
                    para reemplazar equipos completos de acuerdo al criterio técnico en la planta o en el Centro de Servicio
                    Autorizado.
                </p>
            </div>

            <!-- Contact -->
            <div class="terms-section">
                <p class="terms-text" style="text-align: center; margin-top: 15px;">
                    <strong>Ante cualquier consulta usted puede llamar a nuestro Servicio de Atención al Cliente y
                        Asesoramiento.</strong>
                </p>
                <div class="contact-info" style="text-align: center;">
                    <p><strong>GREEKYA - Hardware Oficial de Alto Rendimiento</strong></p>
                    <p>🌐 www.greekya.com | ✉️ soporte@greekya.com</p>
                </div>
            </div>
        </div>
    </body>

    </html>
    <?php
    exit;
}
?>