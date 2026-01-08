<?php
//Borra cualquier cosa que se haya intentado escribir antes
while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

//Inicia la sesión para saber quién está conectado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 0);//No ensuciar la salida con texto de errores

//Importa los archivos necesarios
require_once 'database/database.php';
require_once 'model/producto.php';
require_once 'model/productoDAO.php';

//Funcion para enviar respuestas JSON
function respuestaJSON($estado, $data = null, $mensaje = '', $codigo = 200) {
    //Limpiamos para que no haya nada
    if (ob_get_length()) ob_clean(); 
    
    //Avisa que viene JSON
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code($codigo);
    
    //Convierte el array de PHP en un texto que JavaScript puede leer
    echo json_encode([
        'estado' => $estado,
        'data' => $data,
        'mensaje' => $mensaje
    ], JSON_UNESCAPED_UNICODE);
    
    // Cerramos el buffer y enviamos
    ob_end_flush();
    exit;
}

try {

    if (!isset($_SESSION['usuario'])) {
        respuestaJSON('Fallido', null, 'Sesión no válida o expirada', 401);
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $dao = new productoDAO();
    $id_admin = $_SESSION['usuario']->getId();

    switch ($metodo) {
        case 'GET':
            if (isset($_GET['id'])) {
                $producto = productoDAO::getProductoByID(intval($_GET['id']));
                $producto ? respuestaJSON('Exito', $producto) : respuestaJSON('Fallido', null, 'No encontrado', 404);
            } else {
                $productos = productoDAO::getProductos();
                respuestaJSON('Exito', $productos);
            }
            break;

        case 'POST':
            $input = file_get_contents("php://input");
            $data = json_decode($input, true);
            if (isset($data['nombre'], $data['precio_unidad'], $data['cantidad'])) {
                $res = $dao->crear(
                    $data['nombre'],
                    $data['descripcion'] ?? '',
                    $data['precio_unidad'],
                    $data['cantidad'],
                    $data['imagen'] ?? 'default.png',
                    $id_admin
                );
                $res ? respuestaJSON('Exito', null, 'Producto creado', 201) : respuestaJSON('Fallido', null, 'Error BD', 500);
            } else {
                respuestaJSON('Fallido', null, 'Datos incompletos', 400);
            }
            break;

        case 'PUT':
            $input = file_get_contents("php://input");
            $data = json_decode($input, true);
            if (isset($data['id_producto'])) {
                $res = $dao->modificar(
                    $data['id_producto'],
                    $data['nombre'],
                    $data['descripcion'] ?? '',
                    $data['precio_unidad'],
                    $data['cantidad'],
                    $data['imagen'] ?? 'default.png',
                    $id_admin
                );
                $res ? respuestaJSON('Exito', null, 'Producto actualizado') : respuestaJSON('Fallido', null, 'Error al actualizar', 500);
            } else {
                respuestaJSON('Fallido', null, 'ID no proporcionado', 400);
            }
            break;

        case 'DELETE':
            $input = file_get_contents("php://input");
            $data = json_decode($input, true);
            $id = $data['id'] ?? $_GET['id'] ?? null;
            if ($id) {
                $res = $dao->eliminar($id, $id_admin);
                $res ? respuestaJSON('Exito', null, 'Producto eliminado') : respuestaJSON('Fallido', null, 'Error al borrar', 500);
            } else {
                respuestaJSON('Fallido', null, 'ID no proporcionado', 400);
            }
            break;

        default:
            respuestaJSON('Fallido', null, 'Método no permitido', 405);
            break;
    }

} catch (Exception $e) {
    respuestaJSON('Fallido', null, 'Error crítico: ' . $e->getMessage(), 500);
}
?>