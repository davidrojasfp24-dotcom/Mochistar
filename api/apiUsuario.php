<?php
/**
 * IMPORTANTE: No debe haber NADA antes del <?php.
 */

// 1. Limpieza absoluta del buffer.
while (ob_get_level() > 0) {
    ob_end_clean();
}
ob_start();

/**
 * 2. CONFIGURACIÓN Y SESIÓN
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 0); 

/**
 * 3. IMPORTACIÓN DE ARCHIVOS
 */
require_once 'database/database.php';
require_once 'model/usuario.php';
require_once 'model/usuarioDAO.php';

/**
 * Función para responder al Frontend
 */
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
    /**
     * 4. CONTROL DE ACCESO (Solo administradores)
     */
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
        respuestaJSON('Fallido', null, 'Acceso no autorizado', 403);
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    $dao = new usuarioDAO();
    $id_admin_logeado = $_SESSION['usuario']->getId();

    /**
     * 5. LÓGICA DE LA API
     */
    switch ($metodo) {
        case 'GET':
            if (isset($_GET['id'])) {
                $usuario = usuarioDAO::getUsuarioByID(intval($_GET['id']));
                $usuario ? respuestaJSON('Exito', $usuario) : respuestaJSON('Fallido', null, 'Usuario no encontrado', 404);
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
                $res ? respuestaJSON('Exito', null, 'Usuario creado', 201) : respuestaJSON('Fallido', null, 'Error al crear usuario', 500);
            } else {
                respuestaJSON('Fallido', null, 'Datos obligatorios faltantes', 400);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['id'])) {
                $res = $dao->modificar(
                    $data['id'],
                    $data['nombre'],
                    $data['apellido'] ?? '',
                    $data['email'],
                    $data['telefono'] ?? '',
                    $data['rol'] ?? 'cliente',
                    $id_admin_logeado
                );
                $res ? respuestaJSON('Exito', null, 'Usuario actualizado') : respuestaJSON('Fallido', null, 'Error al actualizar', 500);
            } else {
                respuestaJSON('Fallido', null, 'ID de usuario no proporcionado', 400);
            }
            break;

        case 'DELETE':
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $data['id'] ?? $_GET['id'] ?? null;
            if ($id) {
                $res = $dao->eliminar($id, $id_admin_logeado);
                $res ? respuestaJSON('Exito', null, 'Usuario eliminado') : respuestaJSON('Fallido', null, 'Error al borrar', 500);
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