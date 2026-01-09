<?php
//Importamos la conexión y el modelo de los pedidos
require_once 'database/database.php';
require_once 'model/pedido.php';

class pedidoDAO {

    //Función para sacar todos los pedidos de la base de datos
    public static function getPedidos() {
        //Conectamos con la base de datos
        $con = DataBase::connect();
        
        //Ordenamos por ID de forma descendente para que los pedidos más recientes salgan arriba
        $stmt = $con->prepare("SELECT * FROM pedido ORDER BY id_pedido DESC");
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

    //Guarda todas las acciones importantes que hace el administrador
    private function registrarLog($id_user, $accion, $detalle, $tabla) {
        $con = DataBase::connect();
        //Insertamos los datos en la tabla de logs para que nada se pierda
        $stmt = $con->prepare("INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada) VALUES (?, ?, ?, ?)");
        
        //i = número, s = texto. Ponemos los datos en orden
        $stmt->bind_param("isss", $id_user, $accion, $detalle, $tabla);
        $stmt->execute();
        
        $con->close();
    }
}