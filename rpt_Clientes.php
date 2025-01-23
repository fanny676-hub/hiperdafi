<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/ClienteModel.php");
include(ROOT_CORE . "/fpdf/fpdf.php");

class PDF extends FPDF {
    function convertxt($p_txt) {
        return iconv('UTF-8', 'ISO-8859-1', $p_txt);
    }

    function Header() {
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, "Reporte de Clientes - Casa Matriz", 0, 1, 'C');

        $currentDate = date('d/m/Y');
        $currentTime = date('H:i:s');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, $this->convertxt("Fecha de impresion: $currentDate $currentTime"), 0, 3, 'R');
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 11, $this->convertxt("Página ") . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Configurar la zona horaria adecuada para Bolivia
date_default_timezone_set('America/La_Paz');

$rpt = new ClienteModel();
$records = $rpt->findall();
$records = $records['DATA'];

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Definir los anchos de las celdas
$widths = array(15, 30, 60, 30);  // Ajustar los anchos de las celdas si es necesario

// Cabecera
$pdf->SetFont('Helvetica', 'B', 10);
$header = array("Nro", $pdf->convertxt("CI/NIT"), $pdf->convertxt("Razon Social"), $pdf->convertxt("totalCobro"));

for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($widths[$i], 7, $header[$i], 1);
}
$pdf->Ln();

// Cuerpo
$pdf->SetFont('Arial', '', 10);

$nro_columna = 1; // Inicializar el número de columna

foreach ($records as $row) {
    $pdf->Cell($widths[0], 6, $pdf->convertxt($nro_columna), 1); // Agregar el número de columna
    $pdf->Cell($widths[1], 6, $pdf->convertxt($row['id_cliente']), 1);
    $pdf->Cell($widths[2], 6, $pdf->convertxt($row['razon_social']), 1);
    $pdf->Cell($widths[3], 6, $pdf->convertxt("NULL"), 1);
    $pdf->Ln();
    $nro_columna++; // Incrementar el número de columna
}

$pdf->Output();
?>
