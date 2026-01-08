<?php
while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ocultar errores de texto para no romper el JSON
error_reporting(E_ALL);
ini_set('display_errors', 0); 

require_once 'database/database.php';
require_once 'model/usuario.php';
require_once 'model/usuarioDAO.php';

function respuestaJSON($estado, $data = null, $mensaje = '', $codigo = 200) {
    if (ob_get_length()) ob_clean(); 
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code($codigo);
    echo json_encode([
        'estado' => $estado,
        'data' => $data,
        'mensaje' => $mensaje
    ], JSON_UNESCAPED_UNICODE);
    ob_end_flush();
    exit;
}

try {
    // Verificación de sesión (Asegúrate de estar logueado como admin)
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
        respuestaJSON('Fallido', null, 'Sesión no válida o no eres admin', 403);
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $dao = new usuarioDAO();
    $id_admin_logeado = $_SESSION['usuario']->getId();

    switch ($metodo) {
        case 'GET':
            if (isset($_GET['id'])) {
                $u = usuarioDAO::getUsuarioByID(intval($_GET['id']));
                $u ? respuestaJSON('Exito', $u) : respuestaJSON('Fallido', null, 'No encontrado', 404);
            } else {
                $usuarios = usuarioDAO::getUsuarios();
                respuestaJSON('Exito', $usuarios);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
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
                $res ? respuestaJSON('Exito', null, 'Usuario creado', 201) : respuestaJSON('Fallido', null, 'Error BD', 500);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents("php://input"), true);
            
            //Verificamos que llegue id_usuario (como se llama en tu base de datos)
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

                // Si $res es true enviamos éxito, si no, enviamos fallo
                if ($res) {
                    respuestaJSON('Exito', null, 'Usuario actualizado');
                } else {
                    respuestaJSON('Fallido', null, 'Error al actualizar en la base de datos', 500);
                }
            } else {
                respuestaJSON('Fallido', null, 'ID de usuario no proporcionado', 400);
            }
            break;

        case 'DELETE':
            case 'DELETE':
            // Leemos el cuerpo de la petición
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Obtenemos el ID (enviado como 'id' desde el JS)
            $id_a_borrar = $data['id'] ?? null;

            if ($id_a_borrar) {
                // Llamamos al DAO (asegúrate de que el DAO use id_usuario en el WHERE)
                $res = $dao->eliminar($id_a_borrar, $id_admin_logeado);
                
                if ($res) {
                    respuestaJSON('Exito', null, 'Usuario eliminado');
                } else {
                    respuestaJSON('Fallido', null, 'No se pudo eliminar de la base de datos');
                }
            } else {
                respuestaJSON('Fallido', null, 'ID de usuario no recibido');
            }
            break;
            }
        } catch (Exception $e) {
            respuestaJSON('Fallido', null, 'Error: ' . $e->getMessage(), 500);
}