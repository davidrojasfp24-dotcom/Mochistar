<?php
    include_once 'model/productoDAO.php';

class carritoController {

    public function ver_carrito(){
        //Usamos el DAO para conectarse a la base de datos
        $productos = ProductoDAO::getProductos(); 

        //Ponemos lo que queremos ver en la vista
        $view = 'view/carrito/carrito.php';

        //La variable ya existe y la vista la puede usar
        include_once 'view/main.php';
    }
}
?>