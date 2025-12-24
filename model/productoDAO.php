<?php
require_once 'database/database.php';
require_once 'model/producto.php';

class productoDAO {

    /**
     * Obtiene un producto específico por su ID
     */
    public static function getProductoByID($id){
        $con = DataBase::connect();
        // Asegúrate de que la columna en tu DB sea id_producto
        $stmt = $con->prepare("SELECT * FROM producto WHERE id_producto = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        // Mapea el resultado directamente a la clase producto
        $producto = $results->fetch_object('producto');
        $con->close();

        return $producto;
    }

    /**
     * Obtiene todos los productos de la base de datos
     */
    public static function getProductos(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM producto ORDER BY id_producto DESC");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaProductos = [];
        while ($producto = $results->fetch_object('producto')) {
            $listaProductos[] = $producto;
        }

        $con->close();
        return $listaProductos;
    }

    /**
     * Inserta un nuevo producto y registra la acción en el log
     */
    public function crear($nombre, $descripcion, $precio, $cantidad, $imagen, $id_usuario) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO producto (nombre, descripcion, precio_unidad, cantidad, imagen) VALUES (?, ?, ?, ?, ?)");
        
        // s = string, d = double (precio), i = integer (cantidad)
        $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $cantidad, $imagen);
        
        $crear = $stmt->execute();
        if ($crear) {
            $this->registrarLog($id_usuario, 'INSERT', "Creada mochila: $nombre", 'producto');
        }

        $con->close();
        return $crear;
    }

    /**
     * Actualiza un producto existente
     */
    public function modificar($id, $nombre, $descripcion, $precio, $cantidad, $imagen, $id_usuario) {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE producto SET nombre=?, descripcion=?, precio_unidad=?, cantidad=?, imagen=? WHERE id_producto=?");
        
        $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $cantidad, $imagen, $id);
        
        $success = $stmt->execute();
        if ($success) {
            $this->registrarLog($id_usuario, 'UPDATE', "Modificado producto ID: $id", 'producto');
        }
        $con->close();
        return $success;
    }

    /**
     * Elimina un producto de la base de datos
     */
    public function eliminar($id, $id_admin) {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        
        $success = $stmt->execute();
        if ($success) {
            $this->registrarLog($id_admin, 'DELETE', "Eliminado producto ID: $id", 'producto');
        }
        $con->close();
        return $success;
    }

    /**
     * Función privada para auditoría de acciones de administrador
     */
    private function registrarLog($user, $acc, $det, $tabla) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user, $acc, $det, $tabla);
        $stmt->execute();
        $con->close();
    }
}