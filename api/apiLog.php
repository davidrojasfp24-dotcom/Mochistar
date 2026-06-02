<?php
// API REST para el historial de logs de administración
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/../model/logDAO.php';

// Verificar que el usuario tenga permisos de administrador
verificarAdmin();

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        obtenerLogs();
        break;
    default:
        respuestaJSON('Fallido', null, 'Método no permitido', 405);
}

// Obtener la lista completa de logs
function obtenerLogs() {
    try {
        $logs = logDAO::getLogs();
        $listaFinal = [];

        foreach ($logs as $l) {
            $listaFinal[] = [
                'id_log'         => $l['id_log'],
                'id_usuario'     => $l['id_usuario'],
                'accion'         => $l['accion'],
                'detalle'        => $l['detalle'],
                'tabla_afectada' => $l['tabla_afectada'],
                'fecha'          => date("d/m/Y H:i", strtotime($l['dia/hora']))
            ];
        }

        respuestaJSON('Exito', $listaFinal);
    } catch (Exception $e) {
        respuestaJSON('Fallido', null, 'Error al obtener los logs: ' . $e->getMessage(), 500);
    }
}
