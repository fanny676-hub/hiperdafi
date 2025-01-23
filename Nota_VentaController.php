<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/Detalle_CajaModel.php");

// Procesar el método de la solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Enrutador de métodos
switch ($method) {
    case 'GET': // Consultar
        handleGet($input);
        break;
    case 'POST': // Procesar cierre de caja
        cerrarCaja();
        break;
    default: // Método no soportado
        http_response_code(405);
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Método no soportado']);
        break;
}

// Función para manejar solicitudes GET
function handleGet($input) {
    $p_ope = $input['ope'] ?? $_GET['ope'] ?? null;

    if (!$p_ope) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Operación no especificada']);
        return;
    }

    switch (strtolower($p_ope)) {
        case 'filterid':
            filterId($input);
            break;
        case 'filtersearch':
            filterPaginateAll($input);
            break;
        case 'filterall':
            filterAll();
            break;
        case 'filterdate':
            filterDate($input);
            break;
        default:
            echo json_encode(['ESTADO' => false, 'ERROR' => 'Operación no válida']);
            break;
    }
}

function filterAll() {
    $obj_Detalle = new Detalle_CajaModel();
    $result = $obj_Detalle->findAll();
    echo json_encode($result);
}

function filterId($input) {
    $p_id_detalle = $input['id_detalle'] ?? $_GET['id_detalle'] ?? null;

    if (!$p_id_detalle) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'ID del detalle no especificado']);
        return;
    }

    $obj_Detalle = new Detalle_CajaModel();
    $result = $obj_Detalle->findId($p_id_detalle);
    echo json_encode($result);
}

function filterPaginateAll($input) {
    $page = $input['page'] ?? $_GET['page'] ?? 1;
    $filter = $input['filter'] ?? $_GET['filter'] ?? '';
    $nro_record_page = 10;
    $offset = ($page - 1) * $nro_record_page;

    $obj_Detalle = new Detalle_CajaModel();
    $result = $obj_Detalle->findPaginateAll($filter, $nro_record_page, $offset);
    echo json_encode($result);
}

function filterDate($input) {
    $p_inicio = $input['inicio'] ?? $_GET['inicio'] ?? null;
    $p_fin = $input['fin'] ?? $_GET['fin'] ?? null;

    if (!$p_inicio || !$p_fin) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Fechas incompletas para filtrar']);
        return;
    }

    $obj_Detalle = new Detalle_CajaModel();
    $result = $obj_Detalle->findDate($p_inicio, $p_fin);
    echo json_encode($result);
}

// Función para procesar el cierre de caja
function cerrarCaja() {
    $monto_final = $_POST['monto_final'] ?? null;

    if (!$monto_final) {
        echo '<script>alert("El monto final es obligatorio.");</script>';
        echo '<script>window.location.href = "' . HTTP_BASE . '/caja/cerrar";</script>';
        return;
    }

    $detalleCajaModel = new Detalle_CajaModel();
    $cajaAbierta = $detalleCajaModel->findLast();

    if (!$cajaAbierta || empty($cajaAbierta['DATA'])) {
        echo '<script>alert("No hay caja abierta para cerrar.");</script>';
        echo '<script>window.location.href = "' . HTTP_BASE . '/caja/cerrar";</script>';
        return;
    }

    $id_detalle = $cajaAbierta['DATA'][0]['id_detalle'];
    $resultado = $detalleCajaModel->cerrarCaja($id_detalle, $monto_final);

    if ($resultado['ESTADO']) {
        echo json_encode(['estado' => true, 'mensaje' => 'Caja cerrada correctamente.']);

        //echo '<script>alert("Caja cerrada correctamente.");</script>';
        echo '<script>window.location.href = "' . HTTP_BASE . '/home";</script>';
    } else {
        echo '<script>alert("Error al cerrar la caja.");</script>';
        echo '<script>window.location.href = "' . HTTP_BASE . '/caja/cerrar";</script>';
    }
}
?>
