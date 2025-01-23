<?php
//esto se usa para definir las cabeceras http
header("Access-Control-Allow-Origin: *"); //permite que el origen sea cualquiera
header("Access-Control-Allow-Methods: PUT, GET, POST"); //el servidor permitira medotos put get post
header("Access-Control-Allow-Header: Origin, X-Request-Width, Content-Type, Accept"); //
header("Content-Type: application/json; charset=UTF-8"); //nos dice que se devolvera un json

session_start();


require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php"); //importamos la configuracion global
require_once(ROOT_DIR . "/model/ProductoModel.php"); //importamos el modelo de Producto

//que metodo vendra:
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true); //recibimos json lo decodeamos, y parametrizamos lo valoress

try {
    $Path_Info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIGIN_PATH_INFO']) ? $_SERVER['ORIGIN_PATH_INFO'] : '');

    $request = explode('/', trim($Path_Info, '/'));
} catch (Exception $e) {

    echo $e->getMessage();
}
//aca haremos un switch case para los metodos que tendremos
switch ($method)
{
    case 'GET':
        $p_ope = !empty($input['ope']) ? $input['ope'] : $_GET['ope'];
        if (!empty($p_ope)) {
            if ($p_ope == 'filterall' || $p_ope == 'filterAll') {
                filterAll($input);
            } elseif ($p_ope == 'filterId' || $p_ope=='filterid') {
                filterId($input);
            } elseif ($p_ope == 'filterSearch' || $p_ope == 'filtersearch') {
                filterPaginateAll($input);
            }
        }
        break;
    case 'POST':
        $p_ope = !empty($input['ope']) ? $input['ope'] : $_GET['ope'];
        if(!empty($p_ope) && $p_ope == 'insert')
        {
            insert($input);
        }
        break;
    case 'PUT':
        update($input);
        break;
    case 'DELETE':
        delete($input);
        break;
    default:
        echo 'No soportado';
}


function filterAll($input)
{
    $obj_Producto = new ProductoModel();
    $var = $obj_Producto->findall();
    echo json_encode($var);
}

function filterId($input)
{
    //aca consulto por el &id_producto=x
    $p_id_producto = !empty($input['id_producto']) ? $input['id_producto'] : $_GET['id_producto'];
    $obj_Producto = new ProductoModel();
    $var = $obj_Producto->findid($p_id_producto);
    echo json_encode($var);
}
function filterPaginateAll($input)
{
    $page = !empty($input['page']) ? $input['page'] : $_GET['page'];
    $filter = !empty($input['filter']) ? $input['filter'] : $_GET['filter'];
    $nro_record_page = 10;
    $p_limit = 10;
    $p_offset=0;
    $p_offset=abs(($page-1)* $nro_record_page);

    $obj_Producto = new ProductoModel();
    $var = $obj_Producto->findpaginateall($filter,$p_limit,$p_offset);
    echo json_encode($var);

}

function insert($input)
{
    $p_descripcion_producto = !empty($input['descripcion_producto']) ? $input['descripcion_producto'] : $_POST['descripcion_producto'];
    $p_precio_producto = !empty($input['precio_producto']) ? $input['precio_producto'] : $_POST['precio_producto'];
    $p_estado_producto = !empty($input['estado_producto']) ? $input['estado_producto'] : $_POST['estado_producto'];
    $p_categoria_id_categoria = !empty($input['categoria_id_categoria']) ? $input['categoria_id_categoria'] : $_POST['categoria_id_categoria'];


    $obj_Producto = new ProductoModel();
    $var = $obj_Producto->insert($p_descripcion_producto, $p_precio_producto, $p_estado_producto,$p_categoria_id_categoria);
    echo json_encode($var);

}
function update($input)
{
    $p_id_producto = !empty($input['id_producto']) ? $input['id_producto'] : $_POST['id_producto'];
    $p_descripcion_producto = !empty($input['descripcion_producto']) ? $input['descripcion_producto'] : $_POST['descripcion_producto'];
    $p_precio_producto = !empty($input['precio_producto']) ? $input['precio_producto'] : $_POST['precio_producto'];
    $p_estado_producto = !empty($input['estado_producto']) ? $input['estado_producto'] : $_POST['estado_producto'];
    $p_categoria_id_categoria = !empty($input['categoria_id_categoria']) ? $input['categoria_id_categoria'] : $_POST['categoria_id_categoria'];

    $obj_Producto = new ProductoModel();
    $var = $obj_Producto->update($p_id_producto, $p_descripcion_producto, $p_precio_producto, $p_estado_producto,$p_categoria_id_categoria);
    echo json_encode($var);

}
function delete($input)
{
    $p_id_producto = !empty($input['id_producto']) ? $input['id_producto'] : $_POST['id_producto'];
    $obj_Producto = new ProductoModel();
    $var = $obj_Producto->delete($p_id_producto);
    echo json_encode($var);

}
?>