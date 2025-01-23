<?php
include_once ('../core/ModeloBasePDO.php');

class Detalle_PedidoModel extends ModeloBasePDO
{
    public function __construct()
    {
        parent::__construct();
    }

    // Recuperar detalles de pedidos por número de venta
    public function findByVenta($nro_venta)
    {
        $sql = "SELECT 
            p.id_pedido, 
            p.cantidad, 
            p.sub_total,
            pr.descripcion_producto, 
            pr.precio_producto
        FROM pedido p
        JOIN producto pr ON p.producto_id_producto = pr.id_producto
        WHERE p.nota_venta_nro_venta = :nro_venta";

        $params = [[':nro_venta', $nro_venta, PDO::PARAM_INT]];

        try {
            return parent::gselect($sql, $params);
        } catch (Exception $e) {
            return [
                'ESTADO' => false,
                'ERROR' => $e->getMessage(),
                'TRACE' => $e->getTraceAsString()
            ];
        }
    }
}
