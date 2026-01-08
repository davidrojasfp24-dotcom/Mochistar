<?php

// while (ob_get_level() > 0) {
//     ob_end_clean();
// }
// ob_start();

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// error_reporting(E_ALL);
// ini_set('display_errors', 0);

// require_once '/database/database.php';
// require_once '/model/pedido.php';
// require_once '/model/pedidoDAO.php';


// function respuestaJSON($estado, $data = null, $mensaje = '', $codigo = 200)
// {
//     if (ob_get_length()) ob_clean();

//     header("Content-Type: application/json; charset=UTF-8");
//     http_response_code($codigo);

//     echo json_encode([
//         'estado' => $estado,
//         'data' => $data,
//         'mensaje' => $mensaje
//     ], JSON_UNESCAPED_UNICODE);

//     ob_end_flush();
//     exit;
// }

// try {

//     if (!isset($_SESSION['usuario'])) {
//         respuestaJSON('Fallido', null, 'Sesión no válida o expirada', 401);
//     }

//     $metodo = $_SERVER['REQUEST_METHOD'];
//     $dao = new pedidoDAO();
//     $id_admin = $_SESSION['usuario']->getId();

//     switch ($metodo) {
//         case 'GET':
//             $pedidos = pedidoDAO::getPedidos();
//             respuestaJSON('Exito', $pedidos ? $pedidos : []);
//             break;

//         case 'POST':
//             $input = file_get_contents("php://input");
//             $data = json_decode($input, true);

//             if (isset($data['id_pedido'], $data['nuevo_estado'])) {
//                 $res = $dao->modificarEstado(
//                     $data['id_pedido'],
//                     $data['nuevo_estado'],
//                     $id_admin
//                 );

//                 $res ? respuestaJSON('Exito', null, 'Estado del pedido actualizado')
//                     : respuestaJSON('Fallido', null, 'Error al actualizar en la base de datos', 500);
//             } else {
//                 respuestaJSON('Fallido', null, 'Datos incompletos (id_pedido o nuevo_estado)', 400);
//             }
//             break;

//         default:
//             respuestaJSON('Fallido', null, 'Método no permitido', 405);
//             break;
//     }
// } catch (Exception $e) {
//     respuestaJSON('Fallido', null, 'Error crítico: ' . $e->getMessage(), 500);
// }
