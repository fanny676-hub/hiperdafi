<?php
include_once('../core/ModeloBasePDO.php');

class PedidoModel extends ModeloBasePDO
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Método para obtener todos los pedidos asociados a una nota de venta.
     *
     * @param int $p_nota_venta_nro_venta Número de la nota de venta.
     * @return array Datos de los pedidos asociados.
     */
    public function findAllByVenta($p_nota_venta_nro_venta)
    {
        try {
            $sql = "SELECT 
                p.id_pedido, 
                p.cantidad, 
                p.sub_total, 
                pr.descripcion_producto 
            FROM pedido p
            INNER JOIN producto pr ON p.producto_id_producto = pr.id_producto
            WHERE p.nota_venta_nro_venta = :p_nota_venta_nro_venta";

            $params = [[':p_nota_venta_nro_venta', $p_nota_venta_nro_venta, PDO::PARAM_INT]];
            $result = parent::gselect($sql, $params);

            // Validar si se encontraron datos
            if (empty($result['DATA'])) {
                return [
                    'ESTADO' => false,
                    'ERROR' => "No se encontraron pedidos para la nota de venta: $p_nota_venta_nro_venta",
                ];
            }

            return [
                'ESTADO' => true,
                'DATA' => $result['DATA']
            ];
        } catch (Exception $e) {
            return [
                'ESTADO' => false,
                'ERROR' => $e->getMessage()
            ];
        }
    }
}
?>
