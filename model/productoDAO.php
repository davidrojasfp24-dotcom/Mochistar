<?php
// Importamos la conexión a la base de datos y el modelo de producto
require_once 'database/database.php';
require_once 'model/producto.php';

class productoDAO {

    //Buscamos un producto específico usando su ID único
    public static function getProductoByID($id) {
        // Conectamos con la base de datos
        $con = DataBase::connect();
        //Usamos el "?" para que la consulta sea segura contra ataques
        $stmt = $con->prepare("SELECT * FROM producto WHERE id_producto = ?");
        $stmt->bind_param('i', $id); //La "i" indica que el ID es un número entero
        $stmt->execute();
        $results = $stmt->get_result();

        //Guardamos el resultado en un formato que PHP entienda fácilmente
        $producto = $results->fetch_assoc(); 
        $con->close();

        return $producto;
    }

    //Sacamos la lista de todos los productos que tenemos en la base de datos
    public static function getProductos() {
        $con = DataBase::connect();
        //Ordenamos por ID de forma descendente para que los últimos productos creados salgan los primeros
        $stmt = $con->prepare("SELECT * FROM producto ORDER BY id_producto DESC");
        $stmt->execute();
        $results = $stmt->get_result();

        //Creamos una lista vacía y la vamos rellenando con cada producto
        $listaProductos = [];
        while ($producto = $results->fetch_assoc()) {
            $listaProductos[] = $producto;
        }

        $con->close();
        return $listaProductos;
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

        //Insertamos la acción y usamos NOW() para que la base de datos ponga la hora exacta automáticamente
        $sql = "INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada, `dia/hora`) 
                VALUES (?, ?, ?, ?, NOW())";
                
        $stmt = $con->prepare($sql);
        
        if ($stmt) {
            //Guardamos: ID del admin, qué ha hecho (Insert/Update/Delete), el mensaje y la tabla
            $stmt->bind_param("isss", $id_user, $accion, $detalle, $tabla);
            $stmt->execute();
            $stmt->close();
        }
    }
}