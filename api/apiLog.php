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
                'log_id'     => $l['log_id'],
                'usuario_id' => $l['usuario_id'],
                'mensaje'    => $l['mensaje'],
                'fecha'      => date("d/m/Y H:i", strtotime($l['fecha']))
            ];
        }

        respuestaJSON('Exito', $listaFinal);
    } catch (Exception $e) {
        respuestaJSON('Fallido', null, 'Error al obtener los logs: ' . $e->getMessage(), 500);
    }
}
?>
