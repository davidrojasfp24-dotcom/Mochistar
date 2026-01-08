<?php
require_once 'database/database.php';
require_once 'model/producto.php';

class productoDAO {

    /**
     * Obtiene un producto específico por su ID
     * Usa fetch_assoc para que los nombres de las llaves coincidan con la DB
     */
    public static function getProductoByID($id) {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM producto WHERE id_producto = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $producto = $results->fetch_assoc(); 
        $con->close();

        return $producto;
    }

    /**
     * Obtiene todos los productos de la base de datos
     */
    public static function getProductos() {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM producto ORDER BY id_producto DESC");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaProductos = [];
        while ($producto = $results->fetch_assoc()) {
            $listaProductos[] = $producto;
        }

        $con->close();
        return $listaProductos;
    }

    /**
     * Inserta un nuevo producto y registra el LOG
     */
    public function crear($nombre, $descripcion, $precio, $cantidad, $imagen, $id_usuario) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO producto (nombre, descripcion, precio_unidad, cantidad, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $cantidad, $imagen);
        
        $crear = $stmt->execute();

        // Si se crea con éxito, registramos la acción usando la conexión abierta ($con)
        if ($crear) {
            $this->registrarLog($con, $id_usuario, 'INSERT', "Creada mochila: $nombre", 'producto');
        }

        $con->close();
        return $crear;
    }

    /**
     * Actualiza un producto existente y registra el LOG
     */
    public function modificar($id, $nombre, $descripcion, $precio, $cantidad, $imagen, $id_usuario) {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE producto SET nombre=?, descripcion=?, precio_unidad=?, cantidad=?, imagen=? WHERE id_producto=?");
        $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $cantidad, $imagen, $id);
        
        $success = $stmt->execute();

        if ($success) {
            $this->registrarLog($con, $id_usuario, 'UPDATE', "Modificado producto ID: $id", 'producto');
        }

        $con->close();
        return $success;
    }

    /**
     * Elimina un producto y registra el LOG
     */
    public function eliminar($id, $id_admin) {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        
        $success = $stmt->execute();

        if ($success) {
            $this->registrarLog($con, $id_admin, 'DELETE', "Eliminado producto ID: $id", 'producto');
        }

        $con->close();
        return $success;
    }

    /**
     * Auditoría de acciones (LOGS)
     * Optimizada para usar la conexión $con existente y evitar errores 500
     */
    private function registrarLog($con, $id_user, $accion, $detalle, $tabla) {
        // Evitamos fallos si la sesión expiró
        if (empty($id_user)) return; 

        /**
         * Usamos comillas invertidas en `dia/hora` para evitar errores de sintaxis
         * Usamos NOW() para que SQL gestione la fecha automáticamente.
         */
        $sql = "INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada, `dia/hora`) 
                VALUES (?, ?, ?, ?, NOW())";
                
        $stmt = $con->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("isss", $id_user, $accion, $detalle, $tabla);
            $stmt->execute();
            $stmt->close();
        }
    }
}