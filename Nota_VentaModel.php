<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/HiperDAFI/core/ModeloBasePDO.php');

class Nota_VentaModel extends ModeloBasePDO
{
    public function __construct()
    {
        parent::__construct();
    }

    // Método para obtener una nota de venta por su número
    public function findId($p_nro_venta)
    {
        try {
            $sql = "SELECT 
                nv.nro_venta, 
                nv.fecha_venta, 
                nv.total, 
                nv.cliente_id_cliente,
                cli.razon_social AS cliente_nombre,
                nv.usuario_ci_usuario,
                p.id_pedido, 
                p.cantidad, 
                p.sub_total,
                pr.descripcion_producto, 
                pr.precio_producto
            FROM nota_venta nv
            LEFT JOIN pedido p ON nv.nro_venta = p.nota_venta_nro_venta
            LEFT JOIN producto pr ON p.producto_id_producto = pr.id_producto
            LEFT JOIN cliente cli ON cli.id_cliente = nv.cliente_id_cliente
            WHERE nv.nro_venta = :p_nro_venta";
            
            $params = [[':p_nro_venta', $p_nro_venta, PDO::PARAM_INT]];
            $result = parent::gselect($sql, $params);

            if (empty($result['DATA'])) {
                return [
                    'ESTADO' => false,
                    'ERROR' => "No se encontraron datos para la nota de venta con ID: $p_nro_venta"
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

    // Método para obtener reporte de ventas por fecha
    public function reporteVentasPorFecha($fechaInicio, $fechaFin)
    {
        $sql = "SELECT nv.nro_venta, nv.fecha_venta, nv.total, 
                       cli.razon_social AS cliente, 
                       usr.nombre AS usuario 
                FROM nota_venta nv
                LEFT JOIN cliente cli ON nv.cliente_id_cliente = cli.id_cliente
                LEFT JOIN usuario usr ON nv.usuario_ci_usuario = usr.ci_usuario
                WHERE nv.fecha_venta BETWEEN :fechaInicio AND :fechaFin";

        $params = [
            [':fechaInicio', $fechaInicio, PDO::PARAM_STR],
            [':fechaFin', $fechaFin, PDO::PARAM_STR]
        ];

        return parent::gselect($sql, $params);
    }

    // Método adicional para obtener ventas entre dos fechas
    public function obtenerVentasPorFecha($fechaInicio, $fechaFin)
    {
        $sql = "SELECT * FROM nota_venta WHERE fecha_venta BETWEEN :fechaInicio AND :fechaFin";
        $params = [
            [':fechaInicio', $fechaInicio, PDO::PARAM_STR],
            [':fechaFin', $fechaFin, PDO::PARAM_STR],
        ];
        return parent::gselect($sql, $params);
    }
}
