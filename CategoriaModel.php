<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/core/ModeloBasePDO.php");

/** Modelo: CategoriaModel */
class CategoriaModel extends ModeloBasePDO {
    // Obtener productos por categoría
    public function getProductosPorCategoria($id_categoria) {
        $sql = "SELECT * FROM productos WHERE id_categoria = :id_categoria";
        $params = [[':id_categoria', $id_categoria, PDO::PARAM_INT]];
        return $this->gselect($sql, $params);
    }
    
    // Obtener todas las categorías
    public function findAll() {
        $sql = "SELECT id_categoria, nombre_categoria 
                FROM categoria 
                ORDER BY CAST(id_categoria AS UNSIGNED) ASC"; // Ordena numéricamente por id_categoria
        return parent::gselect($sql, []);
    }
    
    // Obtener una categoría por ID
    public function findId($id_categoria) {
        $sql = "SELECT id_categoria, nombre_categoria FROM categoria WHERE id_categoria = :id_categoria";
        $params = [[':id_categoria', $id_categoria, PDO::PARAM_INT]];
        return parent::gselect($sql, $params);
    }

    // Paginación y búsqueda de categorías
    public function findPaginateAll($filter, $limit, $offset) {
        $sql = "SELECT id_categoria, nombre_categoria 
                FROM categoria 
                WHERE upper(nombre_categoria) LIKE upper(:filter)
                ORDER BY CAST(id_categoria AS UNSIGNED) ASC 
                LIMIT :limit OFFSET :offset";
    
        $params = [
            [':filter', "%$filter%", PDO::PARAM_STR],
            [':limit', $limit, PDO::PARAM_INT],
            [':offset', $offset, PDO::PARAM_INT],
        ];
    
        return $this->gselect($sql, $params);
    }
    
    

    // Insertar una nueva categoría
    public function insert($id_categoria, $nombre_categoria) {
        $sql = "INSERT INTO categoria (id_categoria, nombre_categoria) VALUES (:id_categoria, :nombre_categoria)";
        $params = [
            [':id_categoria', $id_categoria, PDO::PARAM_STR],
            [':nombre_categoria', $nombre_categoria, PDO::PARAM_STR],
        ];
        return parent::ginsert($sql, $params);
    }
    

    // Actualizar una categoría
    public function update($id_categoria, $nombre_categoria) {
        $sql = "UPDATE categoria SET nombre_categoria = :nombre_categoria WHERE id_categoria = :id_categoria";
        $params = [
            [':id_categoria', $id_categoria, PDO::PARAM_INT],
            [':nombre_categoria', $nombre_categoria, PDO::PARAM_STR],
        ];
        return parent::gupdate($sql, $params);
    }
}

/** Modelo: Nota_VentaModel */
class Nota_VentaModel extends ModeloBasePDO {
    // Obtener todas las notas de venta
    public function findAll() {
        $sql = "SELECT id_categoria, nombre_categoria FROM categoria ORDER BY id_categoria ASC";
        return parent::gselect($sql, []);
    }
    

    // Obtener una nota de venta por ID
    public function findId($nro_venta) {
        $sql = "SELECT * FROM nota_venta WHERE nro_venta = :nro_venta";
        $params = [[':nro_venta', $nro_venta, PDO::PARAM_INT]];
        return parent::gselect($sql, $params);
    }

    // Filtrar notas de venta por rango de fechas
    public function filterDate($fecha_inicio, $fecha_fin) {
        $sql = "SELECT * FROM nota_venta WHERE fecha_venta BETWEEN :fecha_inicio AND :fecha_fin ORDER BY fecha_venta DESC";
        $params = [
            [':fecha_inicio', $fecha_inicio, PDO::PARAM_STR],
            [':fecha_fin', $fecha_fin, PDO::PARAM_STR],
        ];
        return parent::gselect($sql, $params);
    }

    // Insertar una nueva nota de venta
    public function insert($total, $cliente_id_cliente, $usuario_ci_usuario) {
        $sql = "INSERT INTO nota_venta (fecha_venta, total, cliente_id_cliente, usuario_ci_usuario)
                VALUES (CURDATE(), :total, :cliente_id_cliente, :usuario_ci_usuario)";
        $params = [
            [':total', $total, PDO::PARAM_STR],
            [':cliente_id_cliente', $cliente_id_cliente, PDO::PARAM_INT],
            [':usuario_ci_usuario', $usuario_ci_usuario, PDO::PARAM_STR],
        ];
        return parent::ginsert($sql, $params);
    }
}

/** Modelo: Detalle_CajaModel */
class Detalle_CajaModel extends ModeloBasePDO {
    // Obtener todos los detalles de caja
    public function findAll() {
        $sql = "SELECT * FROM detalle_caja ORDER BY fecha_inicio DESC, hora_inicio DESC";
        return parent::gselect($sql, []);
    }

    // Obtener un detalle de caja por ID
    public function findId($id_detalle) {
        $sql = "SELECT * FROM detalle_caja WHERE id_detalle = :id_detalle";
        $params = [[':id_detalle', $id_detalle, PDO::PARAM_INT]];
        return parent::gselect($sql, $params);
    }

    // Abrir caja
    public function abrirCaja($monto_inicio) {
        $sql = "INSERT INTO detalle_caja (monto_inicio, fecha_inicio, hora_inicio)
                VALUES (:monto_inicio, CURDATE(), CURTIME())";
        $params = [[':monto_inicio', $monto_inicio, PDO::PARAM_STR]];
        return parent::ginsert($sql, $params);
    }

    // Cerrar caja
    public function cerrarCaja($id_detalle, $monto_final) {
        $sql = "UPDATE detalle_caja 
                SET monto_final = :monto_final, fecha_fin = CURDATE(), hora_fin = CURTIME()
                WHERE id_detalle = :id_detalle";
        $params = [
            [':id_detalle', $id_detalle, PDO::PARAM_INT],
            [':monto_final', $monto_final, PDO::PARAM_STR],
        ];
        return parent::gupdate($sql, $params);
    }
}
