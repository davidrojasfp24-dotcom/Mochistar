<?php
// while (ob_get_level() > 0) ob_end_clean();
// ob_start();

// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Origin: *");

// require_once '../database/database.php';
// require_once '../model/logDAO.php';

// if (session_status() === PHP_SESSION_NONE) session_start();

// function respuestaJSON($estado, $data = null, $mensaje = '')
// {
//     if (ob_get_length()) ob_clean();
//     echo json_encode([
//         'estado' => $estado,
//         'data' => $data,
//         'mensaje' => $mensaje
//     ], JSON_UNESCAPED_UNICODE);
//     exit;
// }

// if (!isset($_SESSION['usuario'])) {
//     respuestaJSON('Fallido', null, 'Acceso denegado: Sesión no válida');
// }

// $metodo = $_SERVER['REQUEST_METHOD'];

// if ($metodo === 'GET') {
//     try {
//         $logs = logDAO::getLogs();

//         $listaFinal = [];

//         foreach ($logs as $l) {

//             $listaFinal[] = [
//                 'id_log'         => $l['id_log'],
//                 'id_usuario'     => $l['id_usuario'],
//                 'accion'         => $l['accion'],
//                 'detalle'        => $l['detalle'],
//                 'tabla_afectada' => $l['tabla_afectada'],
//                 'fecha'          => date("d/m/Y H:i", strtotime($l['dia/hora']))
//             ];
//         }

//         respuestaJSON('Exito', $listaFinal);
//     } catch (Exception $e) {
//         respuestaJSON('Fallido', null, 'Error: ' . $e->getMessage());
//     }
// } else {
//     http_response_code(405);
//     respuestaJSON('Fallido', null, 'Método no permitido');
// }
