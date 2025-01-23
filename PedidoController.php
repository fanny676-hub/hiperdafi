<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/PedidoModel.php");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            echo json_encode(['ESTADO' => false, 'ERROR' => 'Operación GET no soportada']);
            break;

        case 'POST':
            insert($input);
            break;

        default:
            echo json_encode(['ESTADO' => false, 'ERROR' => 'Método no soportado']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
}

public function insertar($cantidad, $sub_total, $nota_venta_numero_venta, $producto_id_producto) {
    $sql = "INSERT INTO pedido (cantidad, sub_total, nota_venta_nro_venta, producto_id_producto)
            VALUES (:cantidad, :sub_total, :nota_venta_numero_venta, :producto_id_producto)";
    
    $params = [
        [':cantidad', $cantidad, PDO::PARAM_INT],
        [':sub_total', $sub_total, PDO::PARAM_STR],
        [':nota_venta_numero_venta', $nota_venta_numero_venta, PDO::PARAM_INT],
        [':producto_id_producto', $producto_id_producto, PDO::PARAM_INT]
    ];

    return $this->ginsert($sql, $params); // Utilizando una función genérica del modelo base
}

