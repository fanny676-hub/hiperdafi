<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/Nota_VentaModel.php"); // Agrega los modelos necesarios

<?php
class ReporteController
{
    public function filtrarVentas()
    {
        $fechaInicio = $_GET['fechaInicio'] ?? null;
        $fechaFin = $_GET['fechaFin'] ?? null;

        if (!$fechaInicio || !$fechaFin) {
            echo '<script>alert("Debes proporcionar ambas fechas.");</script>';
            echo '<script>window.location.href = "' . HTTP_BASE . '/home";</script>';
            return;
        }

        // Genera el reporte utilizando el modelo o lógica que tengas
        $reporteModel = new Nota_VentaModel(); // Asegúrate de tener este modelo
        $reporteData = $reporteModel->obtenerVentasPorFecha($fechaInicio, $fechaFin);

        require(ROOT_VIEW . '/report/visualizarReporteVentas.php'); // Cambia si usas otra vista
    }
}

