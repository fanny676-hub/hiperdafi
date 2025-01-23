<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/Nota_VentaModel.php");
include ROOT_CORE . "/fpdf/fpdf.php";

$nro_venta = $_GET['nro_venta'] ?? null;
if (!$nro_venta) {
    die("Error: Número de venta no especificado.");
}

$model = new Nota_VentaModel();
$data = $model->findId($nro_venta);

// Validar si los datos están disponibles
if (empty($data) || empty($data['DATA'])) {
    die("Error: No se encontraron datos para la nota de venta.");
}

$data = $data['DATA']; // Acceder al contenido real de los datos

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "Reporte de Nota de Venta", 0, 1, 'C');
        $this->Ln(10);
    }
}

// Crear PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Validar índices antes de acceder
$venta = $data[0] ?? null;

if ($venta) {
    // Mostrar datos de la nota de venta
    $pdf->Cell(0, 10, "Nro Venta: " . ($venta['nro_venta'] ?? 'N/A'), 0, 1);
    $pdf->Cell(0, 10, "Fecha: " . ($venta['fecha_venta'] ?? 'N/A'), 0, 1);
    $pdf->Cell(0, 10, "Cliente: " . ($venta['cliente_id_cliente'] ?? 'N/A'), 0, 1);
    $pdf->Cell(0, 10, "Usuario: " . ($venta['usuario_ci_usuario'] ?? 'N/A'), 0, 1);
    $pdf->Ln(5);
} else {
    $pdf->Cell(0, 10, "Error: No se encontró información de la venta.", 0, 1);
    $pdf->Output();
    exit;
}

// Mostrar tabla de pedidos
$pdf->Cell(10, 10, "ID", 1);
$pdf->Cell(60, 10, "Descripción", 1);
$pdf->Cell(20, 10, "Cantidad", 1);
$pdf->Cell(20, 10, "Subtotal", 1);
$pdf->Ln();

foreach ($data as $row) {
    $pdf->Cell(10, 10, $row['id_pedido'] ?? 'N/A', 1);
    $pdf->Cell(60, 10, $row['descripcion_producto'] ?? 'N/A', 1);
    $pdf->Cell(20, 10, $row['cantidad'] ?? 'N/A', 1);
    $pdf->Cell(20, 10, $row['sub_total'] ?? 'N/A', 1);
    $pdf->Ln();
}

// Salida
$pdf->Output();
?>
