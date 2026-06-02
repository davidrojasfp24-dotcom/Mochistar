<?php
// API REST para gestión de productos
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/../model/productoDAO.php';

// Verificar que el usuario tenga permisos de administrador
verificarAdmin();

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        if (isset($_GET['id'])) {
            obtenerProducto($_GET['id']);
        } else {
            obtenerProductos();
        }
        break;
    case 'POST':
        crearProducto();
        break;
    case 'PUT':
        actualizarProducto();
        break;
    case 'DELETE':
        // El ID de producto a eliminar se puede recibir por parámetro GET (?id=...) o en el cuerpo JSON
        if (isset($_GET['id'])) {
            eliminarProducto($_GET['id']);
        } else {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = isset($data['id']) ? $data['id'] : (isset($data['id_producto']) ? $data['id_producto'] : null);
            if ($id) {
                eliminarProducto($id);
            } else {
                respuestaJSON('Fallido', null, 'ID de producto requerido', 400);
            }
        }
        break;
    default:
        respuestaJSON('Fallido', null, 'Método no permitido', 405);
}

// Obtener todos los productos
function obtenerProductos() {
    $productos = productoDAO::getProductos();
    respuestaJSON('Exito', $productos ? $productos : []);
}

// Obtener un producto específico
function obtenerProducto($id) {
    $producto = productoDAO::getProductoByID(intval($id));
    if ($producto) {
        respuestaJSON('Exito', $producto);
    } else {
        respuestaJSON('Fallido', null, 'Producto no encontrado', 404);
    }
}

// Crear un nuevo producto
function crearProducto() {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['nombre']) || !isset($data['precio_unidad']) || !isset($data['cantidad'])) {
        respuestaJSON('Fallido', null, 'Datos incompletos: nombre, precio_unidad y cantidad son requeridos', 400);
        return;
    }

    $nombre = $data['nombre'];
    $descripcion = isset($data['descripcion']) ? $data['descripcion'] : '';
    $precio = floatval($data['precio_unidad']);
    $cantidad = intval($data['cantidad']);
    $imagen = isset($data['imagen']) ? $data['imagen'] : 'default.png';

    $id_admin = isset($_SESSION['usuario']) ? $_SESSION['usuario']->getId() : 0;

    $dao = new productoDAO();
    $resultado = $dao->crear($nombre, $descripcion, $precio, $cantidad, $imagen, $id_admin);

    if ($resultado) {
        respuestaJSON('Exito', null, 'Producto creado correctamente', 201);
    } else {
        respuestaJSON('Fallido', null, 'Error al crear el producto en la base de datos', 500);
    }
}

// Actualizar un producto
function actualizarProducto() {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id_producto'])) {
        respuestaJSON('Fallido', null, 'ID de producto requerido', 400);
        return;
    }

    $id_producto = intval($data['id_producto']);
    
    // Obtener el producto actual de la BD para conservar campos no especificados
    $productoActual = productoDAO::getProductoByID($id_producto);
    if (!$productoActual) {
        respuestaJSON('Fallido', null, 'Producto no encontrado', 404);
        return;
    }

    $nombre = isset($data['nombre']) ? $data['nombre'] : $productoActual['nombre'];
    $descripcion = isset($data['descripcion']) ? $data['descripcion'] : $productoActual['descripcion'];
    $precio = isset($data['precio_unidad']) ? floatval($data['precio_unidad']) : floatval($productoActual['precio_unidad']);
    $cantidad = isset($data['cantidad']) ? intval($data['cantidad']) : intval($productoActual['cantidad']);
    $imagen = isset($data['imagen']) ? $data['imagen'] : $productoActual['imagen'];

    $id_admin = isset($_SESSION['usuario']) ? $_SESSION['usuario']->getId() : 0;

    $dao = new productoDAO();
    $resultado = $dao->modificar($id_producto, $nombre, $descripcion, $precio, $cantidad, $imagen, $id_admin);

    if ($resultado) {
        respuestaJSON('Exito', null, 'Producto actualizado correctamente');
    } else {
        respuestaJSON('Fallido', null, 'Error al actualizar el producto en la base de datos', 500);
    }
}

// Eliminar un producto
function eliminarProducto($id) {
    $id_producto = intval($id);

    // Obtener info del producto antes de eliminarlo para validar existencia
    $producto = productoDAO::getProductoByID($id_producto);
    if (!$producto) {
        respuestaJSON('Fallido', null, 'Producto no encontrado', 404);
        return;
    }

    $id_admin = isset($_SESSION['usuario']) ? $_SESSION['usuario']->getId() : 0;

    $dao = new productoDAO();
    $resultado = $dao->eliminar($id_producto, $id_admin);

    if ($resultado) {
        respuestaJSON('Exito', null, 'Producto eliminado correctamente');
    } else {
        respuestaJSON('Fallido', null, 'Error al eliminar el producto de la base de datos', 500);
    }
}
