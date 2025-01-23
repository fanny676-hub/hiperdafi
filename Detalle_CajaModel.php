<?php
require_once(ROOT_DIR . "/core/ModeloBasePDO.php");

class Detalle_CajaModel extends ModeloBasePDO
{
    // Método para obtener el último registro de la caja abierta
    public function findLast() {
        $sql = "SELECT * FROM detalle_caja ORDER BY id_detalle DESC LIMIT 1";
        $param = [];
        return parent::gselect($sql, $param);
    }
    
    

    // Método para abrir una nueva caja
    public function abrirCaja($monto_inicio, $fecha_hora_inicio) {
        $sql = "INSERT INTO detalle_caja (monto_inicio, fecha_inicio) 
                VALUES (:monto_inicio, :fecha_inicio)";
        $param = [
            [':monto_inicio', $monto_inicio, PDO::PARAM_STR],
            [':fecha_inicio', $fecha_hora_inicio, PDO::PARAM_STR],
        ];
    
        return parent::ginsert($sql, $param);
    }
    

    // Método para cerrar la caja existente
    public function cerrarCaja($id_detalle, $monto_final, $fecha_hora_fin) {
        $sql = "UPDATE detalle_caja 
                SET monto_final = :monto_final, fecha_fin = :fecha_fin 
                WHERE id_detalle = :id_detalle";
        $param = [
            [':monto_final', $monto_final, PDO::PARAM_STR],
            [':fecha_fin', $fecha_hora_fin, PDO::PARAM_STR],
            [':id_detalle', $id_detalle, PDO::PARAM_INT],
        ];
    
        try {
            return parent::gupdate($sql, $param);
        } catch (Exception $e) {
            return ['ESTADO' => false, 'ERROR' => $e->getMessage()];
        }
    }
    
}
