<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");

session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/HiperDAFI/config/global.php");
require_once(ROOT_DIR . "/model/Detalle_CajaModel.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['monto_inicio'])) {
        abrirCaja();
    } elseif (isset($_POST['monto_final'])) {
        cerrarCaja();
    } else {
        echo '<script>alert("Operación no especificada.");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/caja";</script>';
        exit;
    }
} else {
    echo '<script>alert("Método no soportado.");</script>';
    echo '<script>window.location.href ="' . HTTP_BASE . '/caja";</script>';
    exit;
}

function abrirCaja() {
    $monto_inicio = $_POST['monto_inicio'] ?? null;
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $hora_inicio = $_POST['hora_inicio'] ?? date('H:i:s');

    if (!$monto_inicio) {
        echo '<script>alert("El monto inicial es obligatorio.");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/caja/abrir";</script>';
        exit;
    }

    $detalleCajaModel = new Detalle_CajaModel();
    $resultado = $detalleCajaModel->abrirCaja($monto_inicio, "$fecha_inicio $hora_inicio");

    if (isset($resultado['ESTADO']) && $resultado['ESTADO']) {
        echo '<script>alert("Caja abierta correctamente.");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/home";</script>';
        exit;
    } else {
        $error = $resultado['ERROR'] ?? 'Error desconocido';
        echo '<script>alert("Error al abrir la caja: ' . htmlspecialchars($error) . '");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/caja/abrir";</script>';
        exit;
    }
}

function cerrarCaja() {
    $monto_final = $_POST['monto_final'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
    $hora_fin = $_POST['hora_fin'] ?? date('H:i:s');

    if (!$monto_final) {
        echo '<script>alert("El monto final es obligatorio.");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/caja/cerrar";</script>';
        exit;
    }

    $detalleCajaModel = new Detalle_CajaModel();
    $cajaAbierta = $detalleCajaModel->findLast();

    if (!$cajaAbierta || empty($cajaAbierta['DATA'])) {
        echo '<script>alert("No hay caja abierta para cerrar.");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/caja/cerrar";</script>';
        exit;
    }

    $id_detalle = $cajaAbierta['DATA'][0]['id_detalle'];
    $resultado = $detalleCajaModel->cerrarCaja($id_detalle, $monto_final, "$fecha_fin $hora_fin");

    if (isset($resultado['ESTADO']) && $resultado['ESTADO']) {
        echo '<script>alert("Caja cerrada correctamente.");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/home";</script>';
        exit;
    } else {
        $error = $resultado['ERROR'] ?? 'Error desconocido';
        echo '<script>alert("Error al cerrar la caja: ' . htmlspecialchars($error) . '");</script>';
        echo '<script>window.location.href ="' . HTTP_BASE . '/caja/cerrar";</script>';
        exit;
    }
}
