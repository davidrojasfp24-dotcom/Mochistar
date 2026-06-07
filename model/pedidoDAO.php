<?php
//Importamos la conexión y el modelo de los pedidos
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/pedido.php';

class pedidoDAO {

    //Función para sacar todos los pedidos de la base de datos (con nombre del usuario)
    public static function getPedidos() {
        //Conectamos con la base de datos
        $con = DataBase::connect();
        
        // JOIN con usuario para mostrar el nombre del comprador en el panel admin
        $stmt = $con->prepare("
            SELECT p.id_pedido, p.estado, p.fecha, p.precio, p.id_usuario,
                   CONCAT(u.nombre, ' ', u.apellido) AS nombre_usuario, u.email
            FROM pedido p
            LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
            ORDER BY p.id_pedido DESC
        ");
        $stmt->execute();
        $results = $stmt->get_result();

        //Guardamos los pedidos en una lista que JavaScript pueda entender fácilmente
        $listaPedidos = [];
        while ($pedido = $results->fetch_assoc()) {
            $listaPedidos[] = $pedido;
        }

        $con->close();
        return $listaPedidos;
    }

    // Obtener los pedidos de un usuario concreto para mostrarlos en su perfil
    public static function getPedidosByUsuario($id_usuario) {
        $con = DataBase::connect();

        $stmt = $con->prepare("
            SELECT id_pedido, estado, fecha, precio, id_usuario
            FROM pedido
            WHERE id_usuario = ?
            ORDER BY fecha DESC, id_pedido DESC
        ");
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        $results = $stmt->get_result();

        $listaPedidos = [];
        while ($pedido = $results->fetch_assoc()) {
            $pedido['lineas'] = self::getLineasByPedido(intval($pedido['id_pedido']));
            $listaPedidos[] = $pedido;
        }

        $stmt->close();
        $con->close();
        return $listaPedidos;
    }

    // Obtener las líneas (productos) de un pedido concreto
    public static function getLineasByPedido($id_pedido) {
        $con = DataBase::connect();
        $stmt = $con->prepare("
            SELECT lp.id_linea, lp.cantidad, lp.precio_unidad, lp.porcentaje_descuento,
                   pr.nombre AS nombre_producto, pr.imagen
            FROM linea_pedido lp
            LEFT JOIN producto pr ON lp.id_producto = pr.id_producto
            WHERE lp.id_pedido = ?
        ");
        $stmt->bind_param('i', $id_pedido);
        $stmt->execute();
        $results = $stmt->get_result();

        $lineas = [];
        while ($linea = $results->fetch_assoc()) {
            $lineas[] = $linea;
        }

        $con->close();
        return $lineas;
    }

    //Buscamos un pedido concreto usando su ID único
    public static function getPedidoByID($id) {
        $con = DataBase::connect();
        //Usamos el "?" para que la consulta sea segura y evitar ataques
        $stmt = $con->prepare("SELECT * FROM pedido WHERE id_pedido = ?");
        $stmt->bind_param('i', $id); // La "i" significa que el ID es un número entero
        $stmt->execute();
        $results = $stmt->get_result();

        //Cogemos el pedido encontrado
        $pedido = $results->fetch_assoc(); 
        $con->close();

        return $pedido;
    }

    //Cambiamos el estado de un pedido (por ejemplo, de 'Pendiente' a 'Enviado')
    public function modificarEstado($id_pedido, $nuevo_estado, $id_admin) {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE pedido SET estado = ? WHERE id_pedido = ?");
        
        $stmt->bind_param("si", $nuevo_estado, $id_pedido);
        
        $success = $stmt->execute();
        
        //Si el cambio se guarda bien, dejamos una "huella" en el historial de seguridad
        if ($success) {
            $detalle = "Cambio de estado del pedido #$id_pedido a: $nuevo_estado";
            //Guardamos quién lo hizo, qué hizo y en qué tabla
            $this->registrarLog($id_admin, 'UPDATE', $detalle, 'pedido');
        }

        $con->close();
        return $success;
    }

    // Crear un pedido completo con sus líneas de pedido (transacción atómica)
    public static function crearPedido($id_usuario, $total, $lineas) {
        $con = DataBase::connect();
        $con->begin_transaction();

        try {
            // 1. Insertar el pedido principal
            $fecha  = date('Y-m-d H:i:s');
            $estado = 'Pendiente';
            $stmt = $con->prepare("INSERT INTO pedido (estado, fecha, precio, id_usuario) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssdi', $estado, $fecha, $total, $id_usuario);
            $stmt->execute();
            $id_pedido = $stmt->insert_id;
            $stmt->close();

            // 2. Insertar cada línea de pedido (sin porcentaje_descuento: DEFAULT NULL en BD)
            $stmtLinea = $con->prepare(
                "INSERT INTO linea_pedido (precio_unidad, cantidad, id_pedido, id_producto) VALUES (?, ?, ?, ?)"
            );
            if (!$stmtLinea) {
                throw new \RuntimeException('prepare linea_pedido falló: ' . $con->error);
            }
            // bind_param con referencias: se asignan antes del bucle, se actualizan dentro
            $precio  = 0.0;
            $cantidad = 0;
            $id_prod  = 0;
            $stmtLinea->bind_param('diii', $precio, $cantidad, $id_pedido, $id_prod);
            foreach ($lineas as $linea) {
                $precio   = floatval($linea['precio']);
                $cantidad = intval($linea['cantidad']);
                $id_prod  = intval($linea['id']);
                if (!$stmtLinea->execute()) {
                    throw new \RuntimeException('execute linea_pedido falló: ' . $stmtLinea->error);
                }
            }
            $stmtLinea->close();

            $con->commit();
            $con->close();
            return $id_pedido;

        } catch (\Throwable $e) {
            // Capturamos tanto Exception como Error (TypeError, etc.)
            $con->rollback();
            $con->close();
            error_log('[pedidoDAO::crearPedido] ' . $e->getMessage());
            return false;
        }
    }

    //Guarda todas las acciones importantes que hace el administrador
    private function registrarLog($id_user, $accion, $detalle, $tabla) {
        try {
            $con = DataBase::connect();
            //Insertamos los datos en la tabla de logs para que nada se pierda
            $stmt = $con->prepare("INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada) VALUES (?, ?, ?, ?)");
            
            if ($stmt) {
                //i = número, s = texto. Ponemos los datos en orden
                $stmt->bind_param("isss", $id_user, $accion, $detalle, $tabla);
                $stmt->execute();
                $stmt->close();
            }
            $con->close();
        } catch (Exception $e) {
            // Ignoramos errores de registro de log para evitar que la operación principal falle
        }
    }
}
