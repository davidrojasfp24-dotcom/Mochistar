<?php
//Borra cualquier cosa que se haya intentado escribir antes para no romper el JSON
while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

//Inicia la sesión para saber quién está conectado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ocultar errores de texto para no ensuciar la salida de datos
error_reporting(E_ALL);
ini_set('display_errors', 0); 

//Importa los archivos necesarios para los usuarios
require_once 'database/database.php';
require_once 'model/usuario.php';
require_once 'model/usuarioDAO.php';

//Función para enviar respuestas JSON de forma limpia
function respuestaJSON($estado, $data = null, $mensaje = '', $codigo = 200) {
    // Limpiamos el buffer para que no haya texto extra
    if (ob_get_length()) ob_clean(); 
    
    //Avisa al navegador que lo que viene es un JSON
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code($codigo);
    
    //Convierte el array de PHP en un texto que JavaScript puede leer
    echo json_encode([
        'estado' => $estado,
        'data' => $data,
        'mensaje' => $mensaje
    ], JSON_UNESCAPED_UNICODE);
    
    //Cerramos el buffer y enviamos la respuesta
    ob_end_flush();
    exit;
}

try {
    //Revisar si la sesión está iniciada y si el usuario tiene permisos de admin
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
        respuestaJSON('Fallido', null, 'Sesión no válida o no eres admin', 403);
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $dao = new usuarioDAO();
    $id_admin_logeado = $_SESSION['usuario']->getId();

    //Hacemos un menú
    switch ($metodo) {
        
        //Hacemos el GET que es para sacar los usuarios de la base de datos
        case 'GET':
            //Cogemos el id si queremos un usuario concreto
            if (isset($_GET['id'])) {
                $u = usuarioDAO::getUsuarioByID(intval($_GET['id']));
                //Si lo encuentra lo devuelve, si no, avisa
                $u ? respuestaJSON('Exito', $u) : respuestaJSON('Fallido', null, 'No encontrado', 404);
            } else {
                //Si no hay id, pide todos los usuarios
                $usuarios = usuarioDAO::getUsuarios();
                respuestaJSON('Exito', $usuarios);
            }
            break;

        //Hacemos el POST que es para crear un nuevo usuario
        case 'POST':
            //Llegan los datos en JSON y los pasamos a array de PHP
            $data = json_decode(file_get_contents("php://input"), true);
            
            //Revisamos que vengan los datos obligatorios
            if (isset($data['nombre'], $data['email'], $data['contrasena'])) {
                $res = $dao->crear(
                    $data['nombre'],
                    $data['apellido'] ?? '',
                    $data['email'],
                    $data['telefono'] ?? '',
                    $data['rol'] ?? 'cliente',
                    $data['contrasena'],
                    $id_admin_logeado
                );
                //Si se crea bien avisamos, si no, error de base de datos
                $res ? respuestaJSON('Exito', null, 'Usuario creado', 201) : respuestaJSON('Fallido', null, 'Error BD', 500);
            }
            break;

        //Hacemos el PUT que es para modificar un usuario existente
        case 'PUT':
            //Pasamos el JSON que llega a un array de PHP
            $data = json_decode(file_get_contents("php://input"), true);
            
            //Verificamos que llegue el id del usuario que queremos cambiar
            if (isset($data['id_usuario'])) {
                $res = $dao->modificar(
                    $data['id_usuario'],
                    $data['nombre'],
                    $data['apellido'] ?? '',
                    $data['email'],
                    $data['telefono'] ?? '',
                    $data['rol'] ?? 'cliente',
                    $id_admin_logeado
                );

                //Si se guarda correctamente avisamos del éxito
                if ($res) {
                    respuestaJSON('Exito', null, 'Usuario actualizado');
                } else {
                    respuestaJSON('Fallido', null, 'Error al actualizar en la base de datos', 500);
                }
            } else {
                //Si no nos dan el id, no sabemos a quién editar
                respuestaJSON('Fallido', null, 'ID de usuario no proporcionado', 400);
            }
            break;

        //Hacemos el DELETE para eliminar un usuario
        case 'DELETE':
            //Leemos los datos que nos envían
            $data = json_decode(file_get_contents("php://input"), true);
            
            //Obtenemos el ID que queremos borrar
            $id_a_borrar = $data['id'] ?? null;

            if ($id_a_borrar) {
                //Llamamos a la función de eliminar del DAO
                $res = $dao->eliminar($id_a_borrar, $id_admin_logeado);
                
                if ($res) {
                    respuestaJSON('Exito', null, 'Usuario eliminado');
                } else {
                    respuestaJSON('Fallido', null, 'No se pudo eliminar de la base de datos');
                }
            } else {
                //Avisamos si no hemos recibido el ID
                respuestaJSON('Fallido', null, 'ID de usuario no recibido');
            }
            break;
    }

//No encuentra base de datos
} catch (Exception $e) {
    respuestaJSON('Fallido', null, 'Error: ' . $e->getMessage(), 500);
}