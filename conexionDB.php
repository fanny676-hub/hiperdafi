<?php
/*Esta clase realiza 2 tipos de conexiones
    la conexion normal, y la conexion PDO
*/
class ConexionBD
{

    private $driver="mysql";
    private $host="127.0.0.1";
    private $port="3306";
    private $user="root";
    private $password="";
    private $dataBase="hiperdafi";
    private $charset="utf8mb4";

    public function __construct()
    {
        //
    }

    public function conexion()
    {
        $con=null;
        if($this->driver == "mysql" || $this->driver == null)
        {
            $con = new mysqli($this->host,$this->user,$this->password,$this->dataBase,$this->port);
            $con->query("SET NAMES '". $this->charset."'");
        }
        return $con;
    }

    public function conexionPDO()
    {
        $pdo=null;
        if($this->driver == "mysql" || $this->driver == null)
        {
            try {
                $pdo=new PDO(
                    'mysql:host='.$this->host. ';port='.$this->port.';dbname='.$this->dataBase,$this->user,$this->password,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                );
                $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                //echo "conectado exitosamente";
            }catch (PDOException $e)
            {
                //echo "Error en la conexion";
                throw new PDOException($e->getMessage(),(int)$e->getCode());

            }
        }
        return $pdo;
    }
}