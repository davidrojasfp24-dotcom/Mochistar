<?php
include_once 'model/producto.php';
include_once 'database/database.php';

class productoController {

    public static function getProductoByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM producto WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $producto = $results->fetch_object('producto');
        $con->close();
        return $producto;
    }

    public static function getProductos(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM producto");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaProductos = [];
        while ($producto = $results->fetch_object('producto')) {
            $listaProductos[] = $producto;
        }

        $con->close();
        return $listaProductos;
    }
}
?>