<?php
// Importamos la conexión a la base de datos y el modelo de producto
require_once 'database/database.php';
require_once 'model/producto.php';

class productoDAO {

    public static function getProductos() {
        $con = DataBase::connect();

        $sql = "SELECT * FROM producto ORDER BY id_producto DESC";
        $stmt = $con->prepare($sql);

        if (!$stmt) {
            die("Error en prepare(): " . $con->error);
        }

        if (!$stmt->execute()) {
            die("Error en execute(): " . $stmt->error);
        }

        $results = $stmt->get_result();
        if (!$results) {
            die("Error al obtener resultados: " . $stmt->error);
        }

        $listaProductos = [];
        while ($producto = $results->fetch_assoc()) {
            $listaProductos[] = $producto;
        }

        $stmt->close();
        $con->close();

        return $listaProductos;
    }

    // Obtener un producto por ID
    public static function getProductoByID($id) {
        $con = DataBase::connect();

        $stmt = $con->prepare("SELECT * FROM producto WHERE id_producto = ?");
        if (!$stmt) {
            die("Error en prepare(): " . $con->error);
        }

        if (!$stmt->bind_param('i', $id)) {
            die("Error en bind_param(): " . $stmt->error);
        }

        if (!$stmt->execute()) {
            die("Error en execute(): " . $stmt->error);
        }

        $results = $stmt->get_result();
        if (!$results) {
            die("Error al obtener resultados: " . $stmt->error);
        }

        $producto = $results->fetch_assoc();

        $stmt->close();
        $con->close();

        return $producto;
    }

    //Añadimos un producto nuevo a la base de datos
    public function crear($nombre, $descripcion, $precio, $cantidad, $imagen, $id_usuario) {
        $con = DataBase::connect();
        //Preparamos la orden de insertar los datos
        $stmt = $con->prepare("INSERT INTO producto (nombre, descripcion, precio_unidad, cantidad, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $cantidad, $imagen);
        
        $crear = $stmt->execute();

        //Si el producto se crea bien, guardamos en el historial quién lo ha hecho
        if ($crear) {
            $this->registrarLog($con, $id_usuario, 'INSERT', "Creada mochila: $nombre", 'producto');
        }

        $con->close();
        return $crear;
    }

    //Modificamos los datos de un producto que ya existe
    public function modificar($id, $nombre, $descripcion, $precio, $cantidad, $imagen, $id_usuario) {
        $con = DataBase::connect();
        //Actualizamos todos los campos del producto seleccionado por su ID
        $stmt = $con->prepare("UPDATE producto SET nombre=?, descripcion=?, precio_unidad=?, cantidad=?, imagen=? WHERE id_producto=?");
        $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $cantidad, $imagen, $id);
        
        $success = $stmt->execute();

        //Si se modifica correctamente, dejamos rastro en los logs de seguridad
        if ($success) {
            $this->registrarLog($con, $id_usuario, 'UPDATE', "Modificado producto ID: $id", 'producto');
        }

        $con->close();
        return $success;
    }

    //Borramos un producto de la tienda
    public function eliminar($id, $id_admin) {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM producto WHERE id_producto = ?");
        $stmt->bind_param("i", $id);
        
        $success = $stmt->execute();

        //Si se borra bien, registramos qué administrador ha eliminado el producto
        if ($success) {
            $this->registrarLog($con, $id_admin, 'DELETE', "Eliminado producto ID: $id", 'producto');
        }

        $con->close();
        return $success;
    }

    //Registra cada movimiento importante para que no haya errores
    private function registrarLog($con, $id_user, $accion, $detalle, $tabla) {
        //Si por algún motivo no sabemos quién es el usuario, no guardamos nada para evitar fallos
        if (empty($id_user)) return; 

        // Componemos el mensaje de log a partir de los datos recibidos
        $mensaje = "[$accion] $detalle en tabla $tabla";

        //Insertamos la acción y dejamos que la base de datos ponga la fecha automáticamente
        $sql = "INSERT INTO logs (usuario_id, mensaje) VALUES (?, ?)";
                
        $stmt = $con->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("is", $id_user, $mensaje);
            $stmt->execute();
            $stmt->close();
        }
    }
}