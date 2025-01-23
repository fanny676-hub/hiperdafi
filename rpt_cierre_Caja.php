<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/Detalle_CajaModel.php");
include(ROOT_CORE . "/fpdf/fpdf.php");

define('BOLIVIANOS', 'Bs.'); // Constante con el símbolo de Bolivianos

// Obtener datos del cierre de caja desde la base de datos
$detalleCajaModel = new Detalle_CajaModel();
$cajaCierre = $detalleCajaModel->findLast(); // Supongamos que esta función devuelve el último cierre de caja

if (!$cajaCierre || empty($cajaCierre['DATA'])) {
    die("No se encontraron datos de cierre de caja.");
}

$cierreDatos = $cajaCierre['DATA'][0];

// Asignar valores dinámicos
$montoInicial = $cierreDatos['monto_inicio'] ?? 0;
$montoFinal = $cierreDatos['monto_final'] ?? 0;
$fechaInicio = $cierreDatos['fecha_inicio'] ?? 'N/A';
$horaInicio = $cierreDatos['hora_inicio'] ?? 'N/A';
$fechaCierre = $cierreDatos['fecha_fin'] ?? 'N/A';
$horaCierre = $cierreDatos['hora_fin'] ?? 'N/A';

// Configurar FPDF
$pdf = new FPDF('P', 'mm', array(80, 180)); // Tamaño ticket 80mm x 150 mm (largo aprox)

// Agregar una página antes de añadir contenido
$pdf->AddPage();

// Agregar logo en la esquina superior izquierda
$logoPath = ROOT_DIR . '/public/images/logo3.png'; // Ruta al logo
$pdf->Image($logoPath, 5, 5, 20); // (ruta, x, y, ancho)

// CABECERA
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(60, 4, 'Cierre de Caja', 0, 1, 'C');
$pdf->SetFont('Helvetica', '', 8);
$pdf->Cell(60, 4, 'HiperDAFI', 0, 1, 'C');
$pdf->Cell(60, 4, 'Av. Portugal Z. Rectangular #345', 0, 1, 'C');
$pdf->Cell(60, 4, 'Cel. 70004901', 0, 1, 'C');
$pdf->Cell(60, 4, 'HiperDAFI.@gmail.com', 0, 1, 'C');

// Configurar la zona horaria adecuada para Bolivia
date_default_timezone_set('America/La_Paz');
$currentDate = date('d/m/Y');
$currentTime = date('H:i:s');

// Agregar fecha y hora actual
$pdf->Ln(1);
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(60, 4, "Fecha Impresion: $currentDate  $currentTime", 0, 1, 'C');

// COLUMNAS
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Ln(2);
$pdf->Cell(40, 6, 'MONTO INICIAL', 0, 0);
$pdf->Cell(20, 6, number_format((float)$montoInicial, 2, ',', '.') . ' ' . BOLIVIANOS, 0, 1, 'R');

$pdf->Cell(40, 6, 'MONTO FINAL', 0, 0);
$pdf->Cell(20, 6, number_format((float)$montoFinal, 2, ',', '.') . ' ' . BOLIVIANOS, 0, 1, 'R');

$pdf->Cell(40, 6, 'FECHA Y HORA INICIAL', 0, 0);
$pdf->Cell(20, 6, "$fechaInicio $horaInicio", 0, 1, 'R');

$pdf->Cell(40, 6, 'FECHA Y HORA CIERRE', 0, 0);
$pdf->Cell(20, 6, "$fechaCierre $horaCierre", 0, 1, 'R');

// Total
$pdf->Ln(3);
$pdf->Cell(40, 6, 'TOTAL', 0, 0);
$total = $montoFinal - $montoInicial;
$pdf->Cell(20, 6, number_format((float)$total, 2, ',', '.') . ' ' . BOLIVIANOS, 0, 1, 'R');

// FIRMAS
$pdf->Ln(15);
$pdf->Cell(60, 0, '', 'T');
$pdf->Ln(3);
$pdf->Cell(60, 4, "FIRMA CAJERO", 0, 1, 'C');

$pdf->Ln(8);
$pdf->Cell(60, 0, '', 'T');
$pdf->Ln(3);
$pdf->Cell(60, 4, "FIRMA ADMIN", 0, 1, 'C');

// PIE DE PAGINA
$pdf->Ln(10);
$texto = '"UNA VEZ DECLARADO SU CIERRE DE CAJA NO SE PUEDE ADICIONAR MAS DINERO"';
$pdf->SetFont('Arial', '', 6);
$pdf->MultiCell(60, 4, $texto, 0, 'C');

// Generar salida del PDF
$pdf->Output('I', 'ticket.pdf');
?>
