<?php

// Importamos lo necesario
require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once('conexionDB.php');

class ModeloBasePDO
{
    private $secret_key;
    protected $connection;

    public function __construct()
    {
        try {
            $this->connection = new ConexionBD();
            $this->secret_key = SECRET_KEY;
            $this->connection = $this->connection->conexionPDO();
        } catch (PDOException $e) {
            die("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    // Cargar parámetros en una consulta preparada
    protected function loadParam($stmt, $params)
    {
        foreach ($params as $param) {
            if (isset($param[2])) {
                $stmt->bindValue($param[0], $param[1], $param[2]);
            } else {
                $stmt->bindValue($param[0], $param[1]);
            }
        }
    }

    // Métodos genéricos para CRUD y procedimientos
    public function gselect($query, $params = [])
    {
        $result = ['ESTADO' => false];
        try {
            $stmt = $this->connection->prepare($query);
            $this->loadParam($stmt, $params);
            $stmt->execute();
            $result['DATA'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result['NRO'] = count($result['DATA']);
            $result['ESTADO'] = true;
        } catch (PDOException $e) {
            $result['ERROR'] = $e->getMessage();
        }
        return $result;
    }

    public function ginsert($query, $params = [])
    {
        return $this->executeQuery($query, $params);
    }

    public function gupdate($query, $params = [])
    {
        return $this->executeQuery($query, $params);
    }

    public function gdelete($query, $params = [])
    {
        return $this->executeQuery($query, $params);
    }

    protected function executeQuery($query, $params)
    {
        $result = ['ESTADO' => false];
        try {
            $stmt = $this->connection->prepare($query);
            $this->loadParam($stmt, $params);
            $stmt->execute();
            $result['ESTADO'] = true;
        } catch (PDOException $e) {
            $result['ERROR'] = $e->getMessage();
        }
        return $result;
    }

    public function gprocedure($query, $params = [])
    {
        return $this->executeQuery($query, $params);
    }

    public function gprocedureSelect($query, $params = [])
    {
        $result = ['ESTADO' => false];
        try {
            $stmt = $this->connection->prepare($query);
            $this->loadParam($stmt, $params);
            $stmt->execute();
            $result['DATA'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $result['NRO'] = count($result['DATA']);
            $result['ESTADO'] = true;
        } catch (PDOException $e) {
            $result['ERROR'] = $e->getMessage();
        }
        return $result;
    }

    // Convertir datos a UTF-8
    protected function utf8Converter($array)
    {
        array_walk_recursive($array, function (&$item) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    // Generar token ficticio
    protected function generateToken()
    {
        return 'TOKEN';
    }

    // Configuración de cabeceras para CORS
    public function settings()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400'); // Cache por 1 día
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            }
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
    }
}
?>
