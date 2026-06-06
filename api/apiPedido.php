<?php
// API REST para gestión de pedidos
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/../model/usuario.php';   // necesario para deserializar $_SESSION['usuario']
include_once __DIR__ . '/../model/pedidoDAO.php';
include_once __DIR__ . '/../model/productoDAO.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Solo admins pueden ver todos los pedidos
        verificarAdmin();
        if (isset($_GET['id'])) {
            obtenerPedido($_GET['id']);
        } else {
            obtenerPedidos();
        }
        break;
    case 'POST':
        // Crear pedido: solo necesita estar logueado (no ser admin)
        if (!isset($_SESSION['usuario'])) {
            respuestaJSON('Fallido', null, 'Debes iniciar sesión para realizar un pedido', 401, true);
        }
        crearPedido();
        break;
    case 'PUT':
        // Cambiar estado: solo admins
        verificarAdmin();
        actualizarEstadoPedido();
        break;
    default:
        respuestaJSON('Fallido', null, 'Método no permitido', 405);
}

// Obtener todos los pedidos
function obtenerPedidos() {
    $pedidos = pedidoDAO::getPedidos();
    respuestaJSON('Exito', $pedidos ? $pedidos : []);
}

// Obtener un pedido específico con sus líneas de detalle
function obtenerPedido($id) {
    $pedido = pedidoDAO::getPedidoByID(intval($id));
    if ($pedido) {
        // Añadimos las líneas (productos) del pedido al objeto de respuesta
        $pedido['lineas'] = pedidoDAO::getLineasByPedido(intval($id));
        respuestaJSON('Exito', $pedido);
    } else {
        respuestaJSON('Fallido', null, 'Pedido no encontrado', 404);
    }
}

// Crear un nuevo pedido desde el carrito
function crearPedido() {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['lineas']) || empty($data['lineas'])) {
        respuestaJSON('Fallido', null, 'El carrito está vacío', 400);
        return;
    }

    $id_usuario = $_SESSION['usuario']->getId();
    $total      = floatval($data['total'] ?? 0);
    $lineas     = $data['lineas'];

    if ($total <= 0) {
        respuestaJSON('Fallido', null, 'El total del pedido debe ser mayor que 0', 400);
        return;
    }

    foreach ($lineas as $linea) {
        if (
            !isset($linea['id']) ||
            !isset($linea['precio']) ||
            !isset($linea['cantidad']) ||
            intval($linea['id']) <= 0 ||
            floatval($linea['precio']) <= 0 ||
            intval($linea['cantidad']) <= 0
        ) {
            respuestaJSON('Fallido', null, 'Las líneas del pedido contienen datos inválidos', 400);
            return;
        }

        $id_producto = intval($linea['id']);
        if (!productoDAO::getProductoByID($id_producto)) {
            respuestaJSON('Fallido', null, "El producto con ID $id_producto ya no existe. Vacía el carrito y vuelve a añadir los productos.", 400);
            return;
        }
    }

    $id_pedido = pedidoDAO::crearPedido($id_usuario, $total, $lineas);

    if ($id_pedido) {
        respuestaJSON('Exito', ['id_pedido' => $id_pedido], 'Pedido creado correctamente', 201);
    } else {
        respuestaJSON('Fallido', null, 'Error al crear el pedido en la base de datos', 500);
    }
}

// Actualizar el estado de un pedido (admin)
function actualizarEstadoPedido() {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id_pedido']) || !isset($data['nuevo_estado'])) {
        respuestaJSON('Fallido', null, 'Datos incompletos: id_pedido y nuevo_estado son requeridos', 400);
        return;
    }

    $id_pedido    = intval($data['id_pedido']);
    $nuevo_estado = $data['nuevo_estado'];
    $id_admin     = $_SESSION['usuario']->getId();

    $dao      = new pedidoDAO();
    $resultado = $dao->modificarEstado($id_pedido, $nuevo_estado, $id_admin);

    if ($resultado) {
        respuestaJSON('Exito', null, 'Estado del pedido actualizado correctamente');
    } else {
        respuestaJSON('Fallido', null, 'Error al actualizar el estado en la base de datos', 500);
    }
}
?>
