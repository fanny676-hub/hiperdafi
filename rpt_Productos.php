<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/ProductoModel.php");
require_once(ROOT_DIR . "/model/CategoriaModel.php");
include(ROOT_CORE . "/fpdf/fpdf.php");

// Eliminar cualquier salida previa al PDF
ob_start();

class PDF extends FPDF {
    function convertxt($p_txt) {
        return iconv('UTF-8', 'ISO-8859-1//IGNORE', $p_txt);
    }

    function Header() {
        // Ruta del logo (asegúrate de que sea un PNG con transparencia y un diseño circular si deseas un efecto de logo redondo)
        $logoPath = ROOT_DIR . '/public/images/logo3.png';
        if (file_exists($logoPath)) {
            // Agregar logo si existe
            $this->Image($logoPath, 10, 6, 30);
        } else {
            // Mensaje de advertencia si no existe el logo
            error_log("El archivo del logo no se encuentra en: " . $logoPath);
        }

        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 20, "Reporte de Productos", 0, 1, 'C');

        $currentDate = date('d/m/Y');
        $currentTime = date('H:i:s');
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 8, $this->convertxt("Fecha de impresion: $currentDate $currentTime"), 0, 3, 'C');
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 11, $this->convertxt("Página ") . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Configurar la zona horaria adecuada
date_default_timezone_set('America/La_Paz');

$productoModel = new ProductoModel();
$records = $productoModel->findall(); // Obtener los productos
$records = $records['DATA']; // Asegurar el acceso a la clave 'DATA'

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Cabecera
$pdf->SetFont('helvetica', 'B', 11);
$header = array($pdf->convertxt("IdProducto"), $pdf->convertxt("Descripcion"), $pdf->convertxt("Precio"), $pdf->convertxt("Estado"), $pdf->convertxt("Categoria"));
$widths = array(30, 40, 20, 20, 40);  // Ajustar los anchos de las celdas si es necesario

// Estilo para la cabecera
$pdf->SetFillColor(173, 216, 230); // Color azul claro de fondo
$pdf->SetTextColor(0, 0, 0); // Color azul nítido para el texto
$pdf->SetDrawColor(0); // Color del borde (negro)

for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($widths[$i], 6, $header[$i], 1, 0, 'C', true);
}
$pdf->Ln();

// Cuerpo
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(224, 235, 255); // Color de fondo para las filas
$pdf->SetTextColor(0); // Color del texto (negro)
$fill = false; // Alternar el color de fondo

$totalProductos = 0; // Contador para el total de productos

foreach ($records as $row) {
    $pdf->Cell($widths[0], 6, $pdf->convertxt($row['id_producto']), 1, 0, 'C', $fill);
    $pdf->Cell($widths[1], 6, $pdf->convertxt($row['descripcion_producto']), 1, 0, 'C', $fill);
    $pdf->Cell($widths[2], 6, number_format($row['precio_producto'], 2), 1, 0, 'C', $fill);
    $pdf->Cell($widths[3], 6, $pdf->convertxt($row['estado_producto']), 1, 0, 'C', $fill);
    $pdf->Cell($widths[4], 6, $pdf->convertxt($row['nombre_categoria']), 1, 0, 'C', $fill);
    $pdf->Ln();
    $fill = !$fill; // Alternar el color de fondo
    $totalProductos++;
}

// Mostrar el total de productos
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Total de Productos: $totalProductos", 0, 1, 'L');

// Enviar salida del PDF
ob_end_clean(); // Limpiar el buffer de salida
$pdf->Output();

?>
