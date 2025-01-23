<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");


session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");

require_once(ROOT_DIR . "/model/UsuarioModel.php");


$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);


try {
    $Path_Info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    $request = explode('/', trim($Path_Info, '/'));
} catch (Exception $e) {
    echo $e->getMessage();
}
switch ($method) {

    case 'POST':
        $p_ope = !empty($input['ope']) ? $input['ope'] : $_POST['ope'];
        if ($p_ope == 'login') {
            login($input);
        } else if ($p_ope == 'register') {
            register($input);
        } else if ($p_ope == 'logout') {
            session_destroy();
        }
        break;
}

function  login($input)
{
    $p_ci_usuario = !empty($input['ci_usuario']) ? $input['ci_usuario'] : $_POST['ci_usuario'];
    $p_password = !empty($input['password']) ? $input['password'] : $_POST['password'];
    $p_password = hash('sha512', md5($p_password));
    $su   = new UsuarioModel();
    $var = $su->verificarlogin($p_ci_usuario, $p_password);
    //var_dump($var);
    if (count($var['DATA']) > 0) {
        $_SESSION['login'] = $var['DATA'][0];
        echo json_encode($var);
        exit();
    } else {
        $array = array();
        $array['ESTADO'] = false;
        $array['ERROR'] = "Usuario o Contraseña no valida, verifique sus datos, demasiados intentos bloqueara al usuario el acceso al sistema.";
        echo json_encode($var);
        exit();
    }
}
function register($input)
{
    $p_ci_usuario = !empty($input['ci_usuario']) ? $input['ci_usuario'] : $_POST['ci_usuario'];
    $p_nombre = !empty($input['nombre']) ? $input['nombre'] : $_POST['nombre'];
    $p_apellido = !empty($input['apellido']) ? $input['apellido'] : $_POST['apellido'];
    $p_fecha_nacimiento = !empty($input['fecha_nacimiento']) ? $input['fecha_nacimiento'] : $_POST['fecha_nacimiento'];
    $p_rol_usuario = !empty($input['rol']) ? $input['rol'] : $_POST['rol'];
    $p_password = !empty($input['password']) ? $input['password'] : $_POST['password'];

    $p_password = hash('sha512', md5($p_password));
    

    $tseg_usuario = new UsuarioModel();
    $var = $tseg_usuario->register($p_ci_usuario,$p_nombre,$p_apellido,$p_fecha_nacimiento,$p_rol_usuario,$p_password);

    echo json_encode($var);
}