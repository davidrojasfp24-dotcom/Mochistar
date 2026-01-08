<?php
require_once 'database/database.php';
require_once 'model/pedido.php';

class pedidoDAO {

    /**
     * Obtiene todos los pedidos de la base de datos
     * Mapeo a array asociativo para compatibilidad total con el JSON de la API
     */
    public static function getPedidos() {
        $con = DataBase::connect();
        // Ordenamos por ID descendente para ver los más nuevos primero
        $stmt = $con->prepare("SELECT * FROM pedido ORDER BY id_pedido DESC");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaPedidos = [];
        while ($pedido = $results->fetch_assoc()) {
            $listaPedidos[] = $pedido;
        }

        $con->close();
        return $listaPedidos;
    }

    /**
     * Obtiene un pedido específico por su ID
     */
    public static function getPedidoByID($id) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM pedido WHERE id_pedido = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $pedido = $results->fetch_assoc(); 
        $con->close();

        return $pedido;
    }

    /**
     * Modifica el estado de un pedido (Pendiente, Enviado, etc.)
     * Además, registra quién hizo el cambio en la tabla log_admin
     */
    public function modificarEstado($id_pedido, $nuevo_estado, $id_admin) {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE pedido SET estado = ? WHERE id_pedido = ?");
        
        $stmt->bind_param("si", $nuevo_estado, $id_pedido);
        
        $success = $stmt->execute();
        
        // Si el cambio fue exitoso, registramos la acción en el historial (Logs)
        if ($success) {
            $detalle = "Cambio de estado del pedido #$id_pedido a: $nuevo_estado";
            $this->registrarLog($id_admin, 'UPDATE', $detalle, 'pedido');
        }

        $con->close();
        return $success;
    }

    /**
     * Sistema de Auditoría: Registra las acciones del administrador
     */
    private function registrarLog($id_user, $accion, $detalle, $tabla) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada) VALUES (?, ?, ?, ?)");
        
        // s = string, i = integer
        $stmt->bind_param("isss", $id_user, $accion, $detalle, $tabla);
        $stmt->execute();
        
        $con->close();
    }
}