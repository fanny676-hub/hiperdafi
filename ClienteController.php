<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/ClienteModel.php");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $Path_Info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    $request = explode('/', trim($Path_Info, '/'));
} catch (Exception $e) {
    echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    exit;
}

// Controlador principal
switch ($method) {
    case 'GET':
        $p_ope = $input['ope'] ?? $_GET['ope'] ?? null;
        if ($p_ope) {
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
                default:
                    echo json_encode(['ESTADO' => false, 'ERROR' => 'Operación no válida']);
            }
        } else {
            echo json_encode(['ESTADO' => false, 'ERROR' => 'Operación no especificada']);
        }
        break;

    case 'POST':
        insert($input);
        break;

    case 'PUT':
        update($input);
        break;

    default:
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Método no soportado']);
        break;
}

function filterAll()
{
    try {
        $obj_Cliente = new ClienteModel();
        $var = $obj_Cliente->findAll();

        echo json_encode([
            'ESTADO' => true,
            'DATA' => $var['DATA'] ?? [],
            'LENGTH' => count($var['DATA'] ?? [])
        ]);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}

function filterId($input)
{
    $p_id_cliente = $input['id_cliente'] ?? $_GET['id_cliente'] ?? null;

    if (!$p_id_cliente) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'ID de cliente no proporcionado']);
        return;
    }

    try {
        $obj_Cliente = new ClienteModel();
        $var = $obj_Cliente->findid($p_id_cliente);

        echo json_encode(['ESTADO' => true, 'DATA' => $var['DATA'] ?? []]);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}

function filterPaginateAll($input)
{
    $page = $input['page'] ?? $_GET['page'] ?? 1;
    $filter = $input['filter'] ?? $_GET['filter'] ?? '';
    $nro_record_page = 10;
    $p_offset = ($page - 1) * $nro_record_page;

    try {
        $obj_Cliente = new ClienteModel();
        $var = $obj_Cliente->findpaginateall($filter, $nro_record_page, $p_offset);

        echo json_encode([
            'ESTADO' => true,
            'DATA' => $var['DATA'] ?? [],
            'LENGTH' => $var['LENGTH'] ?? 0
        ]);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}

function insert($input)
{
    $p_id_cliente = $input['id_cliente'] ?? $_POST['id_cliente'] ?? null;
    $p_razon_social = $input['razon_social'] ?? $_POST['razon_social'] ?? null;

    if (!$p_id_cliente || !$p_razon_social) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Todos los campos son obligatorios']);
        return;
    }

    try {
        $obj_Cliente = new ClienteModel();
        $obj_Cliente->insert($p_id_cliente, $p_razon_social);

        echo json_encode(['ESTADO' => true, 'MENSAJE' => 'Cliente agregado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}

function update($input)
{
    $p_id_cliente = $input['id_cliente'] ?? $_POST['id_cliente'] ?? null;
    $p_razon_social = $input['razon_social'] ?? $_POST['razon_social'] ?? null;

    if (!$p_id_cliente || !$p_razon_social) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Todos los campos son obligatorios']);
        return;
    }

    try {
        $obj_Cliente = new ClienteModel();
        $obj_Cliente->update($p_id_cliente, $p_razon_social);

        echo json_encode(['ESTADO' => true, 'MENSAJE' => 'Cliente actualizado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}
?>
