<?php
include_once "../core/ModeloBasePDO.php";

class UsuarioModel extends ModeloBasePDO
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtener todos los usuarios
     * 
     * Este método consulta todos los usuarios en la base de datos y devuelve sus datos.
     * 
     * @return array Lista de usuarios
     */
    public function findAll()
    {
        $sql = "SELECT ci_usuario, nombre, apellido, fecha_nacimiento, fecha_y_hora_alta, rol_usuario, estado FROM usuario";
        $param = array();
        return parent::gselect($sql, $param);
    }

    /**
     * Buscar usuario por carnet de identidad
     * 
     * Este método busca un usuario específico utilizando su carnet de identidad.
     * 
     * @param string $p_ci_usuario El carnet de identidad del usuario
     * @return array Datos del usuario encontrado
     */
    public function findid($p_ci_usuario)
    {
        $sql = "SELECT ci_usuario, nombre, apellido, fecha_nacimiento, fecha_y_hora_alta, rol_usuario, estado FROM usuario WHERE ci_usuario=:p_ci_usuario";
        $param = array();
        array_push($param, [':p_ci_usuario', $p_ci_usuario, PDO::PARAM_STR]);
        return parent::gselect($sql, $param);
    }

    /**
     * Buscar usuarios con paginación y filtro
     * 
     * Este método busca usuarios aplicando un filtro y paginación, 
     * permitiendo obtener solo una cantidad específica de registros a la vez.
     * 
     * @param string $p_filtro El filtro de búsqueda
     * @param int $p_limit La cantidad de registros a obtener
     * @param int $p_offset El inicio de la búsqueda
     * @return array Lista de usuarios y total de registros
     */
    public function findpaginateall($p_filtro, $p_limit, $p_offset)
    {
        $sql = "SELECT ci_usuario, nombre, apellido, fecha_nacimiento, fecha_y_hora_alta, rol_usuario, estado 
        FROM usuario
        WHERE upper(concat(IFNULL(ci_usuario,''),IFNULL(nombre,''),IFNULL(apellido,''),IFNULL(fecha_nacimiento,''),IFNULL(fecha_y_hora_alta,''),IFNULL(rol_usuario,''),IFNULL(estado,''))) 
        like concat('%',upper(IFNULL(:p_filtro,'')),'%') 
        limit :p_limit
        offset :p_offset";
        
        $param = array();
        array_push($param, [':p_filtro', $p_filtro, PDO::PARAM_STR]);
        array_push($param, [':p_limit', $p_limit, PDO::PARAM_INT]);
        array_push($param, [':p_offset', $p_offset, PDO::PARAM_INT]);

        $var = parent::gselect($sql, $param);

        $sqlcount = "SELECT count(1) as cant
        FROM usuario
        WHERE upper(concat(IFNULL(ci_usuario,''),IFNULL(nombre,''),IFNULL(apellido,''),IFNULL(fecha_nacimiento,''),IFNULL(fecha_y_hora_alta,''),IFNULL(rol_usuario,''),IFNULL(estado,''))) 
        like concat('%',upper(IFNULL(:p_filtro,'')),'%')";
        
        $param = array();
        array_push($param, [':p_filtro', $p_filtro, PDO::PARAM_STR]);
        $var1 = parent::gselect($sqlcount, $param);
        $var['LENGTH'] = $var1['DATA'][0]['cant'];
        
        return $var;
    }

    /**
     * Registrar un nuevo usuario
     * 
     * Este método inserta un nuevo usuario en la base de datos con la información proporcionada.
     * 
     * @param string $p_ci_usuario El carnet de identidad del usuario
     * @param string $p_nombre El nombre del usuario
     * @param string $p_apellido El apellido del usuario
     * @param string $p_fecha_nacimiento La fecha de nacimiento del usuario
     * @param string $p_rol_usuario El rol del usuario (Admin, Mesero, Cajero)
     * @param string $p_password La contraseña del usuario
     * @return bool Resultado de la operación de inserción
     */
    public function register($p_ci_usuario, $p_nombre, $p_apellido, $p_fecha_nacimiento, $p_rol_usuario, $p_password)
    {
        $p_estado = 'ACTIVO';
        $sql = "INSERT INTO usuario(ci_usuario, nombre, apellido, fecha_nacimiento, fecha_y_hora_alta, rol_usuario, password, estado) 
        VALUES (:p_ci_usuario, :p_nombre, :p_apellido, :p_fecha_nacimiento, NOW(), :p_rol_usuario, :p_password, :p_estado)";
        
        $param = array();
        array_push($param, [':p_ci_usuario', $p_ci_usuario, PDO::PARAM_STR]);
        array_push($param, [':p_nombre', $p_nombre, PDO::PARAM_STR]);
        array_push($param, [':p_apellido', $p_apellido, PDO::PARAM_STR]);
        array_push($param, [':p_fecha_nacimiento', $p_fecha_nacimiento, PDO::PARAM_STR]);
        array_push($param, [':p_rol_usuario', $p_rol_usuario, PDO::PARAM_STR]);
        array_push($param, [':p_password', $p_password, PDO::PARAM_STR]);
        array_push($param, [':p_estado', $p_estado, PDO::PARAM_STR]);

        return parent::ginsert($sql, $param);
    }

    /**
     * Actualizar información de un usuario
     * 
     * Este método actualiza los datos de un usuario específico.
     * 
     * @param string $p_ci_usuario El carnet de identidad del usuario
     * @param string $p_nombre El nombre del usuario
     * @param string $p_apellido El apellido del usuario
     * @param string $p_fecha_nacimiento La fecha de nacimiento del usuario
     * @param string $p_rol_usuario El rol del usuario
     * @param string $p_estado El estado del usuario
     * @return bool Resultado de la operación de actualización
     */
    public function update($p_ci_usuario, $p_nombre, $p_apellido, $p_fecha_nacimiento, $p_rol_usuario, $p_estado)
    {
        $sql = "UPDATE usuario 
        SET nombre=:p_nombre,
        apellido=:p_apellido,
        fecha_nacimiento=:p_fecha_nacimiento,
        rol_usuario=:p_rol_usuario,
        estado=:p_estado
        WHERE ci_usuario=:p_ci_usuario";
        
        $param = array();
        array_push($param, [':p_ci_usuario', $p_ci_usuario, PDO::PARAM_STR]);
        array_push($param, [':p_nombre', $p_nombre, PDO::PARAM_STR]);
        array_push($param, [':p_apellido', $p_apellido, PDO::PARAM_STR]);
        array_push($param, [':p_fecha_nacimiento', $p_fecha_nacimiento, PDO::PARAM_STR]);
        array_push($param, [':p_rol_usuario', $p_rol_usuario, PDO::PARAM_STR]);
        array_push($param, [':p_estado', $p_estado, PDO::PARAM_STR]);

        return parent::gupdate($sql, $param);
    }

    /**
     * Verificar el inicio de sesión
     * 
     * Este método verifica las credenciales del usuario para permitir el inicio de sesión.
     * 
     * @param string $p_ci_usuario El carnet de identidad del usuario
     * @param string $p_password La contraseña del usuario
     * @return array Datos del usuario si las credenciales son correctas
     */
    public function verificarLogin($p_ci_usuario, $p_password)
    {
        $sql = "SELECT ci_usuario, nombre, apellido, rol_usuario
        FROM usuario 
        WHERE ci_usuario=:p_ci_usuario AND password=:p_password";
        
        $param = array();
        array_push($param, [':p_ci_usuario', $p_ci_usuario, PDO::PARAM_STR]);
        array_push($param, [':p_password', $p_password, PDO::PARAM_STR]);
        
        return parent::gselect($sql, $param);
    }
}
?>
