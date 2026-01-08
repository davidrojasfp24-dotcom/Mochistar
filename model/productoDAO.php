<?php
require_once 'database/database.php';
require_once 'model/producto.php';

class productoDAO {

    /**
     * Obtiene un producto específico por su ID
     * CORREGIDO: Usa fetch_assoc para que el JSON no salga vacío
     */
    public static function getProductoByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM producto WHERE id_producto = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        // Al usar fetch_assoc, los nombres de las llaves serán 
        // exactamente iguales a los de tu base de datos.
        $producto = $results->fetch_assoc(); 
        $con->close();

        return $producto;
    }

    /**
     * Obtiene todos los productos de la base de datos
     * CORREGIDO: Mapeo a array asociativo para compatibilidad con la API
     */
    public static function getProductos(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM producto ORDER BY id_producto DESC");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaProductos = [];
        // fetch_assoc captura 'id_producto', 'nombre', 'precio_unidad', etc.
        while ($producto = $results->fetch_assoc()) {
            $listaProductos[] = $producto;
        }

        $con->close();
        return $listaProductos;
    }

    /**
     * Inserta un nuevo producto
     */
    public function crear($nombre, $descripcion, $precio, $cantidad, $imagen, $id_usuario) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO producto (nombre, descripcion, precio_unidad, cantidad, imagen) VALUES (?, ?, ?, ?, ?)");
        
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
     * Elimina un producto
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
     * Auditoría de acciones
     */
    private function registrarLog($user, $acc, $det, $tabla) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user, $acc, $det, $tabla);
        $stmt->execute();
        $con->close();
    }
}