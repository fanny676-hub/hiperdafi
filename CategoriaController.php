<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/CategoriaModel.php");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $Path_Info = $_SERVER['PATH_INFO'] ?? $_SERVER['ORIG_PATH_INFO'] ?? '';
    $request = explode('/', trim($Path_Info, '/'));
} catch (Exception $e) {
    echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    exit;
}

switch ($method) {
    case 'GET':
        $p_ope = $input['ope'] ?? $_GET['ope'] ?? '';
        if ($p_ope) {
            switch (strtolower($p_ope)) {
                case 'filterid':
                    filterId($input);
                    break;
                case 'filtersearch':
                    filterPaginateAll($_GET);
                    break;
                case 'filterall':
                    filterAll();
                    break;
                case 'filtrarcategoria':
                    filtrarCategoria($_GET['id_categoria'] ?? null);
                    break;
                default:
                    echo json_encode(['ESTADO' => false, 'ERROR' => 'Operación no válida']);
                    break;
            }
        }
        break;

    case 'POST':
        insert($input);
        break;

    case 'PUT':
        update($input);
        break;

    default:
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Método no soportado']);
        break;
}

// Función para obtener categorías paginadas con filtro
function filterPaginateAll($params) {
    $page = $params['page'] ?? 1;
    $filter = $params['filter'] ?? ''; 
    $recordsPerPage = 10;
    $offset = ($page - 1) * $recordsPerPage;

    try {
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->findPaginateAll($filter, $recordsPerPage, $offset);

        echo json_encode([
            'ESTADO' => true,
            'DATA' => $categorias['DATA'] ?? [],
            'LENGTH' => $categorias['LENGTH'] ?? 0,
            'PAGE' => $page
        ]);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}

// Función para obtener todas las categorías
function filterAll() {
    try {
        $categoriaModel = new CategoriaModel();
        $categorias = $categoriaModel->findAll();

        echo json_encode([
            'ESTADO' => true,
            'DATA' => $categorias['DATA'] ?? [],
            'LENGTH' => count($categorias['DATA'] ?? [])
        ]);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}

// Función para filtrar por ID de categoría
function filterId($input) {
    $id_categoria = $input['id_categoria'] ?? $_GET['id_categoria'] ?? null;

    if (!$id_categoria) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'ID de categoría no proporcionado']);
        return;
    }

    try {
        $categoriaModel = new CategoriaModel();
        $categoria = $categoriaModel->findId($id_categoria);

        echo json_encode(['ESTADO' => true, 'DATA' => $categoria['DATA']]);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}

// Función para insertar una nueva categoría
function insert($input) {
    $id_categoria = $input['id_categoria'] ?? $_POST['id_categoria'] ?? null;
    $nombre_categoria = $input['nombre_categoria'] ?? $_POST['nombre_categoria'] ?? null;

    if (!$id_categoria || !$nombre_categoria) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'ID y Nombre de categoría son obligatorios']);
        return;
    }

    try {
        $categoriaModel = new CategoriaModel();
        $categoriaModel->insert($id_categoria, $nombre_categoria); // Asegúrate de que el modelo reciba estos parámetros

        echo json_encode(['ESTADO' => true, 'MENSAJE' => 'Categoría creada exitosamente']);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}


// Función para actualizar una categoría
function update($input) {
    $id_categoria = $input['id_categoria'] ?? $_POST['id_categoria'] ?? null;
    $nombre_categoria = $input['nombre_categoria'] ?? $_POST['nombre_categoria'] ?? null;

    if (!$id_categoria || !$nombre_categoria) {
        echo json_encode(['ESTADO' => false, 'ERROR' => 'Todos los campos son obligatorios']);
        return;
    }

    try {
        $categoriaModel = new CategoriaModel();
        $categoriaModel->update($id_categoria, $nombre_categoria);

        echo json_encode(['ESTADO' => true, 'MENSAJE' => 'Categoría actualizada exitosamente']);
    } catch (Exception $e) {
        echo json_encode(['ESTADO' => false, 'ERROR' => $e->getMessage()]);
    }
}
