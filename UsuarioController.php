<?php
//esto se usa para definir las cabeceras http
header("Access-Control-Allow-Origin: *"); //permite que el origen sea cualquiera
header("Access-Control-Allow-Methods: PUT, GET, POST"); //el servidor permitira medotos put get post
header("Access-Control-Allow-Header: Origin, X-Request-Width, Content-Type, Accept"); //
header("Content-Type: application/json; charset=UTF-8"); //nos dice que se devolvera un json

session_start();


require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php"); //importamos la configuracion global
require_once(ROOT_DIR . "/model/UsuarioModel.php"); //importamos el modelo de Usuario

//que metodo vendra:
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true); //recibimos json lo decodeamos, y parametrizamos lo valores

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
        insert($input);
        break;
    case 'PUT':
        update($input);
        break;
    // case 'DELETE':
    //     delete($input);
    //     break;
    default:
        echo 'No soportado';
}


function filterAll($input)
{
    $obj_Usuario = new UsuarioModel();
    $var = $obj_Usuario->findall();
    echo json_encode($var);
}

function filterId($input)
{
    //aca consulto por el &id_producto=x
    $p_ci_usuario_usuario = !empty($input['ci_usuario']) ? $input['ci_usuario'] : $_GET['ci_usuario'];
    $obj_Usuario = new UsuarioModel();
    $var = $obj_Usuario->findid($p_ci_usuario_usuario);
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

    $obj_Usuario = new UsuarioModel();
    $var = $obj_Usuario->findpaginateall($filter,$p_limit,$p_offset);
    echo json_encode($var);
}

function insert($input)
{
    $p_ci_usuario = !empty($input['ci_usuario']) ? $input['ci_usuario'] : $_POST['ci_usuario'];
    $p_nombre = !empty($input['nombre']) ? $input['nombre'] : $_POST['nombre'];
    $p_apellido = !empty($input['apellido']) ? $input['apellido'] : $_POST['apellido'];
    $p_fecha_nacimiento = !empty($input['fecha_nacimiento']) ? $input['fecha_nacimiento'] : $_POST['fecha_nacimiento'];
    $p_rol_usuario = !empty($input['rol_usuario']) ? $input['rol_usuario'] : $_POST['rol_usuario'];
    $p_password = !empty($input['password']) ? $input['password'] : $_POST['password'];

    $obj_Usuario = new UsuarioModel();
    $var = $obj_Usuario->register($p_ci_usuario,$p_nombre,$p_apellido,$p_fecha_nacimiento,$p_rol_usuario,$p_password);
    echo json_encode($var);

}
function update($input)
{
    $p_ci_usuario = !empty($input['ci_usuario']) ? $input['ci_usuario'] : $_POST['ci_usuario'];
    $p_nombre = !empty($input['nombre']) ? $input['nombre'] : $_POST['nombre'];
    $p_apellido = !empty($input['apellido']) ? $input['apellido'] : $_POST['apellido'];
    $p_fecha_nacimiento = !empty($input['fecha_nacimiento']) ? $input['fecha_nacimiento'] : $_POST['fecha_nacimiento'];
    $p_rol_usuario = !empty($input['rol_usuario']) ? $input['rol_usuario'] : $_POST['rol_usuario'];
    $p_estado = !empty($input['estado']) ? $input['estado'] : $_POST['estado'];

    $obj_Usuario = new UsuarioModel();
    $var = $obj_Usuario->update($p_ci_usuario,$p_nombre,$p_apellido,$p_fecha_nacimiento,$p_rol_usuario,$p_estado);
    echo json_encode($var);

}
// function delete($input)
// {
//     $p_id_producto = !empty($input['id_producto']) ? $input['id_producto'] : $_POST['id_producto'];
//     $obj_Producto = new ProductoModel();
//     $var = $obj_Producto->delete($p_id_producto);
//     echo json_encode($var);

// }
?>
