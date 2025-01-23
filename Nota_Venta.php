<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
include(ROOT_CORE . "/fpdf/fpdf.php");

// Definir constantes y configuraciones
define('BOLIVIANOS', 'Bs.'); // Constante con el símbolo de Bolivianos

// Obtener número de venta
$nro_venta = $_GET['nro_venta'] ?? '';
if (!$nro_venta) {
    die('Número de venta no especificado.');
}

// Preparar la URL para obtener los detalles de la venta
$url = HTTP_BASE . "/controller/Nota_VentaController.php?ope=filterId&nro_venta=" . urlencode($nro_venta);

// Obtener detalles de la venta
$response = @file_get_contents($url);
if ($response === false) {
    die('Error al conectar con la API.');
}

$responseData = json_decode($response, true);

// Validar la respuesta
if (!$responseData || !isset($responseData['ESTADO']) || $responseData['ESTADO'] !== true || empty($responseData['DATA'])) {
    die('No se encontraron datos para la venta especificada.');
}

$venta = $responseData['DATA'];

// Crear el PDF
$pdf = new FPDF('P', 'mm', 'A4'); // Tamaño A4
$pdf->AddPage();

// CABECERA
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'NOTA DE VENTA', 0, 1, 'C'); // Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, 'HiperDAFI', 0, 1, 'C'); // Nombre de la empresa
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(5);

// Información de la venta
$pdf->Cell(130, 7, 'ID Venta: ' . htmlspecialchars($venta[0]['nro_venta'] ?? ''), 0, 0);
$pdf->Cell(60, 7, 'Fecha: ' . htmlspecialchars($venta[0]['fecha_venta'] ?? ''), 0, 1);

$pdf->Cell(130, 7, 'Razón Social: ' . htmlspecialchars($venta[0]['razon_social'] ?? ''), 0, 0);
$pdf->Cell(60, 7, 'CI/NIT/CEX: ' . htmlspecialchars($venta[0]['usuario_ci_usuario'] ?? ''), 0, 1);

$pdf->Ln(10);

// DETALLES DE LA VENTA
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Detalles de la Venta', 0, 1);

// Encabezados de columnas
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(80, 10, 'Producto', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C');
$pdf->Cell(30, 10, 'Precio Unitario', 1, 0, 'C');
$pdf->Cell(30, 10, 'Subtotal', 1, 1, 'C');

// Mostrar detalles de la venta
$pdf->SetFont('Arial', '', 10);
$total = 0;
foreach ($venta as $row) {
    $descripcion = htmlspecialchars($row['descripcion_producto'] ?? 'N/A');
    $cantidad = htmlspecialchars($row['cantidad'] ?? 0);
    $precio = number_format($row['precio_producto'] ?? 0, 2);
    $subtotal = number_format($row['sub_total'] ?? 0, 2);

    $pdf->Cell(80, 10, $descripcion, 1, 0, 'C');
    $pdf->Cell(30, 10, $cantidad, 1, 0, 'C');
    $pdf->Cell(30, 10, $precio . ' ' . BOLIVIANOS, 1, 0, 'C');
    $pdf->Cell(30, 10, $subtotal . ' ' . BOLIVIANOS, 1, 1, 'C');

    $total += $row['sub_total'] ?? 0;
}

// TOTAL
$pdf->SetFont('Arial', 'B', 12);
$pdf->Ln(5);
$pdf->Cell(130, 10, 'TOTAL VENTA: ' . number_format($total, 2) . ' ' . BOLIVIANOS, 0, 1, 'L');

// Salida del PDF
$pdf->Output('nota_venta_' . $nro_venta . '.pdf', 'I');
?>
