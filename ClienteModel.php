<?php
include_once "../core/ModeloBasePDO.php";

class ClienteModel extends ModeloBasePDO
{
    public function __construct()
    {
        parent::__construct();
    }

    // Consulta 1: Obtener todos los clientes (Consulta simple)
    // Devuelve todos los clientes con sus campos id_cliente y razon_social.
    public function findAll()
    {
        $sql = "SELECT id_cliente, razon_social FROM cliente";
        $param = [];
        return parent::gselect($sql, $param);
    }

    // Consulta 2: Obtener un cliente por su ID (Consulta simple)
    // Busca un cliente por su id_cliente, devolviendo id_cliente y razon_social.
    public function findid($p_id_cliente)
    {
        $sql = "SELECT id_cliente, razon_social FROM cliente WHERE id_cliente=:p_id_cliente";
        $param = [];
        // Se usa PDO::PARAM_STR para manejar IDs alfanuméricos como carnets o NITs con complemento.
        array_push($param, [':p_id_cliente', $p_id_cliente, PDO::PARAM_STR]);
        return parent::gselect($sql, $param);
    }

    // Consulta 3: Obtener clientes con paginación y filtro (Consulta compleja)
    // Permite filtrar clientes por cualquier campo y paginar los resultados.
    public function findpaginateall($p_filtro, $p_limit, $p_offset)
    {
        $sql = "SELECT id_cliente, razon_social 
                FROM cliente 
                WHERE UPPER(CONCAT(IFNULL(id_cliente, ''), IFNULL(razon_social, ''))) 
                LIKE CONCAT('%', UPPER(:p_filtro), '%') 
                LIMIT :p_limit OFFSET :p_offset";

        $param = [];
        array_push($param, [':p_filtro', $p_filtro, PDO::PARAM_STR]);
        array_push($param, [':p_limit', $p_limit, PDO::PARAM_INT]);
        array_push($param, [':p_offset', $p_offset, PDO::PARAM_INT]);

        $result = parent::gselect($sql, $param);

        // Contar el total de registros para la paginación
        $sqlcount = "SELECT COUNT(1) as cant
                     FROM cliente
                     WHERE UPPER(CONCAT(IFNULL(id_cliente, ''), IFNULL(razon_social, ''))) 
                     LIKE CONCAT('%', UPPER(:p_filtro), '%')";

        $param = [];
        array_push($param, [':p_filtro', $p_filtro, PDO::PARAM_STR]);
        $total = parent::gselect($sqlcount, $param);

        $result['LENGTH'] = $total['DATA'][0]['cant'] ?? 0;
        return $result;
    }

    // Consulta 4: Insertar un nuevo cliente (Consulta simple)
    // Inserta un nuevo cliente con su id_cliente y razon_social.
    public function insert($p_id_cliente, $p_razon_social)
    {
        $sql = "INSERT INTO cliente(id_cliente, razon_social) VALUES (:p_id_cliente, :p_razon_social)";
        $param = [];
        array_push($param, [':p_id_cliente', $p_id_cliente, PDO::PARAM_STR]);
        array_push($param, [':p_razon_social', $p_razon_social, PDO::PARAM_STR]);

        return parent::ginsert($sql, $param);
    }

    // Consulta 5: Actualizar un cliente existente (Consulta simple)
    // Modifica la razon_social de un cliente identificado por su id_cliente.
    public function update($p_id_cliente, $p_razon_social)
    {
        $sql = "UPDATE cliente SET razon_social=:p_razon_social WHERE id_cliente=:p_id_cliente";
        $param = [];
        array_push($param, [':p_id_cliente', $p_id_cliente, PDO::PARAM_STR]);
        array_push($param, [':p_razon_social', $p_razon_social, PDO::PARAM_STR]);

        return parent::gupdate($sql, $param);
    }

    // Consulta 6: Contar clientes por inicial de razon_social (Consulta compleja adicional)
    // Agrupa los clientes por la inicial de su razon_social y devuelve el conteo.
    public function countByInitial()
    {
        $sql = "SELECT LEFT(razon_social, 1) as inicial, COUNT(*) as total
                FROM cliente
                GROUP BY LEFT(razon_social, 1)
                ORDER BY inicial ASC";
        $param = [];
        return parent::gselect($sql, $param);
    }
}
?>
