<?php
// API REST para gestión de usuarios
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/../model/usuarioDAO.php';

// Verificar que el usuario tenga permisos de administrador
verificarAdmin();

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        if (isset($_GET['id'])) {
            obtenerUsuario($_GET['id']);
        } else {
            obtenerUsuarios();
        }
        break;
    case 'POST':
        crearUsuario();
        break;
    case 'PUT':
        actualizarUsuario();
        break;
    case 'DELETE':
        // El ID de usuario a eliminar se puede recibir por parámetro GET (?id=...) o en el cuerpo JSON
        if (isset($_GET['id'])) {
            eliminarUsuario($_GET['id']);
        } else {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['id'])) {
                eliminarUsuario($data['id']);
            } elseif (isset($data['id_usuario'])) {
                eliminarUsuario($data['id_usuario']);
            } else {
                respuestaJSON('Fallido', null, 'ID de usuario requerido', 400);
            }
        }
        break;
    default:
        respuestaJSON('Fallido', null, 'Método no permitido', 405);
}

// Obtener todos los usuarios
function obtenerUsuarios() {
    $usuarios = usuarioDAO::getUsuarios();
    $resultado = [];
    
    foreach ($usuarios as $usuario) {
        // Es importante crear un array solo con los campos que queremos obtener para no filtrar contraseña por ejemplo
        $resultado[] = [
            'id_usuario' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'email' => $usuario['email'],
            'telefono' => $usuario['telefono'],
            'rol' => $usuario['rol']
        ];
    }
    
    respuestaJSON('Exito', $resultado);
}

// Obtener un usuario específico
function obtenerUsuario($id) {
    $usuario = usuarioDAO::getUsuarioByID(intval($id));
    
    if ($usuario) {
        $resultado = [
            'id_usuario' => $usuario['id_usuario'],
            'nombre' => $usuario['nombre'],
            'apellido' => $usuario['apellido'],
            'email' => $usuario['email'],
            'telefono' => $usuario['telefono'],
            'rol' => $usuario['rol']
        ];
        respuestaJSON('Exito', $resultado);
    } else {
        respuestaJSON('Fallido', null, 'Usuario no encontrado', 404);
    }
}

// Crear un nuevo usuario
function crearUsuario() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Comprobamos que se pasan los datos obligatorios (soportamos tanto 'password' como 'contrasena')
    $password = isset($data['password']) ? $data['password'] : (isset($data['contrasena']) ? $data['contrasena'] : null);
    
    if (!isset($data['nombre']) || !isset($data['email']) || empty($password)) {
        respuestaJSON('Fallido', null, 'Datos incompletos: nombre, email y contraseña son requeridos', 400);
        return;
    }
    
    $nombre = $data['nombre'];
    $apellido = isset($data['apellido']) ? $data['apellido'] : '';
    $email = $data['email'];
    $telefono = isset($data['telefono']) ? $data['telefono'] : '';
    $rol = isset($data['rol']) ? $data['rol'] : 'cliente';
    
    $id_admin = isset($_SESSION['usuario']) ? $_SESSION['usuario']->getId() : 0;
    
    $dao = new usuarioDAO();
    $resultado = $dao->crear($nombre, $apellido, $email, $telefono, $rol, $password, $id_admin);
    
    if ($resultado) {
        respuestaJSON('Exito', null, 'Usuario creado correctamente', 201);
    } else {
        respuestaJSON('Fallido', null, 'Error al crear el usuario, el email ya podría estar registrado', 400);
    }
}

// Actualizar usuario
function actualizarUsuario() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['id_usuario'])) {
        respuestaJSON('Fallido', null, 'ID de usuario requerido', 400);
        return;
    }
    
    $id_usuario = intval($data['id_usuario']);
    
    // Obtener el usuario actual de la BD para conservar campos no modificados
    $usuarioActual = usuarioDAO::getUsuarioByID($id_usuario);
    if (!$usuarioActual) {
        respuestaJSON('Fallido', null, 'Usuario no encontrado', 404);
        return;
    }
    
    $nombre = isset($data['nombre']) ? $data['nombre'] : $usuarioActual['nombre'];
    $apellido = isset($data['apellido']) ? $data['apellido'] : $usuarioActual['apellido'];
    $email = isset($data['email']) ? $data['email'] : $usuarioActual['email'];
    $telefono = isset($data['telefono']) ? $data['telefono'] : $usuarioActual['telefono'];
    $rol = isset($data['rol']) ? $data['rol'] : $usuarioActual['rol'];
    
    $id_admin = isset($_SESSION['usuario']) ? $_SESSION['usuario']->getId() : 0;
    
    $dao = new usuarioDAO();
    $resultado = $dao->modificar($id_usuario, $nombre, $apellido, $email, $telefono, $rol, $id_admin);
    
    if ($resultado) {
        respuestaJSON('Exito', null, 'Usuario actualizado correctamente');
    } else {
        respuestaJSON('Fallido', null, 'Error al actualizar el usuario', 500);
    }
}

// Eliminar usuario
function eliminarUsuario($id) {
    $id_usuario = intval($id);
    
    // Obtener info del usuario antes de eliminarlo para validar existencia
    $usuario = usuarioDAO::getUsuarioByID($id_usuario);
    if (!$usuario) {
        respuestaJSON('Fallido', null, 'Usuario no encontrado', 404);
        return;
    }
    
    $id_admin = isset($_SESSION['usuario']) ? $_SESSION['usuario']->getId() : 0;
    
    $dao = new usuarioDAO();
    $resultado = $dao->eliminar($id_usuario, $id_admin);
    
    if ($resultado) {
        respuestaJSON('Exito', null, 'Usuario eliminado correctamente');
    } else {
        respuestaJSON('Fallido', null, 'Error al eliminar el usuario', 500);
    }
}