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
ini_set('display_errors', 0); //No ensuciar la salida con texto de errores

//Importa los archivos necesarios
require_once 'database/database.php';
require_once 'model/producto.php';
require_once 'model/productoDAO.php';

//Funcion para enviar respuestas JSON
function respuestaJSON($estado, $data = null, $mensaje = '', $codigo = 200)
{
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
    //Revisar si la sesion esta iniciada
    if (!isset($_SESSION['usuario'])) {
        respuestaJSON('Fallido', null, 'Sesión no válida o expirada', 401);
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $dao = new productoDAO();
    $id_admin = $_SESSION['usuario']->getId();

    //Hacemos un menu para cada metodo
    switch ($metodo) {
        //Hacemos el GET que es para sacar los productos de la base de datos
        case 'GET':
            //Cogemos el id de cada producto
            if (isset($_GET['id'])) {
                $producto = productoDAO::getProductoByID(intval($_GET['id']));
                //Si lo encuentra, lo devuelve, si no, avisa que no lo ha encontrado
                $producto ? respuestaJSON('Exito', $producto) : respuestaJSON('Fallido', null, 'No encontrado', 404);
            } else {
                //Pide los productos
                $productos = productoDAO::getProductos();
                respuestaJSON('Exito', $productos);
            }
            break;
        //Hacemos el POST que es para crear un nuevo producto
        case 'POST':

            //Llegan los datos en JSON
            $input = file_get_contents("php://input");
            //Pasamos el JSON a un array de PHP
            $data = json_decode($input, true);
            //Revisamos que vengan los datos necesarios
            if (isset($data['nombre'], $data['precio_unidad'], $data['cantidad'])) {
                $res = $dao->crear(
                    $data['nombre'],
                    $data['descripcion'] ?? '',
                    $data['precio_unidad'],
                    $data['cantidad'],
                    $data['imagen'] ?? 'default.png',
                    $id_admin
                );
                //Se ha creado correctamente
                $res ? respuestaJSON('Exito', null, 'Producto creado', 201) : respuestaJSON('Fallido', null, 'Error BD', 500);
            //No se ha creado
            } else {
                respuestaJSON('Fallido', null, 'Datos incompletos', 400);
            }
            break;
        //Hacemos el PUT que es para modificar un producto
        case 'PUT':
            //Llegan los datos en JSON
            $input = file_get_contents("php://input");
            //Pasamos el JSON a un array de PHP
            $data = json_decode($input, true);
            //Revisamos que vengan los datos necesarios
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
                //Se ha guardado correctamente
                $res ? respuestaJSON('Exito', null, 'Producto actualizado') : respuestaJSON('Fallido', null, 'Error al actualizar', 500);
            //No se ha modificado
            } else {
                respuestaJSON('Fallido', null, 'ID no proporcionado', 400);
            }
            break;
        //Hacemos el DELETE que es para borrar un producto
        case 'DELETE':
            //Llegan los datos en JSON
            $input = file_get_contents("php://input");
            //Pasamos el JSON a un array de PHP
            $data = json_decode($input, true);
            $id = $data['id'] ?? $_GET['id'] ?? null;
            //Miramos si existe el id
            if ($id) {
                //Se hace la funcion de eliminar
                $res = $dao->eliminar($id, $id_admin);
                $res ? respuestaJSON('Exito', null, 'Producto eliminado') : respuestaJSON('Fallido', null, 'Error al borrar', 500);
            //No se ha encontrado id
            } else {
                respuestaJSON('Fallido', null, 'ID no proporcionado', 400);
            }
            break;
        //Si no es ninguno de los metodos anteriores falla
        default:
            respuestaJSON('Fallido', null, 'Método no permitido', 405);
            break;
    }
//No encuentra base de datos
} catch (Exception $e) {
    respuestaJSON('Fallido', null, 'Error crítico: ' . $e->getMessage(), 500);
}
