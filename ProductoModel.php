<?php
include_once "../core/ModeloBasePDO.php";

class ProductoModel extends ModeloBasePDO
{
    public function __construct()
    {
        parent::__construct();
    }

    // filtrar todo: Consulta simple que devuelve todos los productos junto con sus categorías
    public function findall()
    {
        $sql = "SELECT 
        p.id_producto, 
        p.descripcion_producto, 
        p.precio_producto, 
        p.estado_producto, 
        c.nombre_categoria 
        FROM 
        producto p
        JOIN 
        categoria c 
        ON 
        p.categoria_id_categoria = c.id_categoria;";
        $param = array();
        return parent::gselect($sql, $param);
    }

    // filtrar por id: Consulta simple que busca un producto por su ID
    public function findid($p_id_producto)
    {
        $sql = "SELECT 
        p.id_producto, 
        p.descripcion_producto, 
        p.precio_producto, 
        p.estado_producto, 
        c.nombre_categoria,
        categoria_id_categoria
        FROM 
        producto p
        JOIN 
        categoria c 
        ON 
        p.categoria_id_categoria = c.id_categoria
        WHERE 
        p.id_producto = :p_id_producto";
        $param = array();
        // Se agregan parámetros preparados para prevenir inyección SQL
        array_push($param, [':p_id_producto', $p_id_producto, PDO::PARAM_INT]);
        return parent::gselect($sql, $param);
    }

    // paginación: Consulta compleja que recupera productos según un filtro y parámetros de paginación
    public function findpaginateall($p_filtro, $p_limit, $p_offset)
    {
        $sql = "SELECT 
        p.id_producto, 
        p.descripcion_producto, 
        p.precio_producto, 
        p.estado_producto, 
        c.nombre_categoria,
        p.categoria_id_categoria
        FROM 
        producto p
        JOIN 
        categoria c 
        ON 
        p.categoria_id_categoria = c.id_categoria
        WHERE upper(concat(IFNULL(id_producto,''),IFNULL(descripcion_producto,''),IFNULL(precio_producto,''),IFNULL(estado_producto,''),IFNULL(c.nombre_categoria,''),IFNULL(c.id_categoria,''))) 
        like concat('%',upper(IFNULL(:p_filtro,'')),'%')  
        limit :p_limit
        offset :p_offset"; // Recupera productos filtrados con paginación
        $param = array();
        array_push($param, [':p_filtro', $p_filtro, PDO::PARAM_STR]);
        array_push($param, [':p_limit', $p_limit, PDO::PARAM_INT]);
        array_push($param, [':p_offset', $p_offset, PDO::PARAM_INT]);

        // Consulta principal para los productos filtrados
        $var = parent::gselect($sql, $param);
        
        // Consulta adicional: Cuenta el total de productos filtrados (compleja)
        $sqlcount = "SELECT count(1) as cant
        FROM 
        producto p
        JOIN 
        categoria c 
        ON 
        p.categoria_id_categoria = c.id_categoria
        WHERE upper(concat(IFNULL(id_producto,''),IFNULL(descripcion_producto,''),IFNULL(precio_producto,''),IFNULL(estado_producto,''),IFNULL(c.nombre_categoria,''))) 
        like concat('%',upper(IFNULL(:p_filtro,'')),'%')";
        $param = array();
        array_push($param, [':p_filtro', $p_filtro, PDO::PARAM_STR]);
        $var1 =  parent::gselect($sqlcount, $param);
        $var['LENGTH'] = $var1['DATA'][0]['cant']; // Agrega la longitud total al resultado
        return $var;
    }

    // crear nuevo producto: Consulta simple para insertar un nuevo registro en la base de datos
    public function insert($p_descripcion_producto, $p_precio_producto, $p_estado_producto, $p_categoria_id_categoria)
    {
        $sql = "INSERT INTO producto(descripcion_producto, precio_producto, estado_producto, categoria_id_categoria) 
        VALUES (:p_descripcion_producto,:p_precio_producto,:p_estado_producto,:p_categoria_id_categoria)";
        $param = array();

        // Se utilizan parámetros preparados para proteger contra inyección SQL
        array_push($param, [':p_descripcion_producto', $p_descripcion_producto, PDO::PARAM_STR]);
        array_push($param, [':p_precio_producto', $p_precio_producto, PDO::PARAM_STR]);
        array_push($param, [':p_estado_producto', $p_estado_producto, PDO::PARAM_STR]);
        array_push($param, [':p_categoria_id_categoria', $p_categoria_id_categoria, PDO::PARAM_STR]);

        return parent::ginsert($sql, $param);
    }

    // deshabilitar un producto: Consulta simple para cambiar el estado de un producto a "INACTIVO"
    public function delete($p_id_producto)
    {
        $p_estado_producto = 'INACTIVO';
        $sql = "UPDATE producto SET estado_producto=:p_estado_producto WHERE id_producto=:p_id_producto";
        $param = array();
        array_push($param, [':p_id_producto', $p_id_producto, PDO::PARAM_INT]);
        array_push($param, [':p_estado_producto', $p_estado_producto, PDO::PARAM_STR]);

        return parent::gdelete($sql, $param);
    }

    // actualizar un producto: Consulta simple para modificar un registro existente
    public function update($p_id_producto, $p_descripcion_producto, $p_precio_producto, $p_estado_producto, $p_categoria_id_categoria)
    {
        $sql = "UPDATE producto 
        SET
        descripcion_producto=:p_descripcion_producto,
        precio_producto=:p_precio_producto,
        estado_producto=:p_estado_producto,
        categoria_id_categoria=:p_categoria_id_categoria
        WHERE id_producto=:p_id_producto";
        $param = array();

        // Se agregan parámetros preparados
        array_push($param, [':p_id_producto', $p_id_producto, PDO::PARAM_INT]);
        array_push($param, [':p_descripcion_producto', $p_descripcion_producto, PDO::PARAM_STR]);
        array_push($param, [':p_precio_producto', $p_precio_producto, PDO::PARAM_STR]);
        array_push($param, [':p_estado_producto', $p_estado_producto, PDO::PARAM_STR]);
        array_push($param, [':p_categoria_id_categoria', $p_categoria_id_categoria, PDO::PARAM_STR]);

        return parent::gupdate($sql, $param);
    }
}