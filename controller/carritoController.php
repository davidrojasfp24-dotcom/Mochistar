<?php
    include_once 'model/productoDAO.php';

class carritoController {

    public function ver_carrito(){
        // 1. Aquí es donde se obtienen los productos
        // Usamos el DAO que ya sabe cómo conectarse a la base de datos
        $productos = ProductoDAO::getProductos(); 

        // 2. Definimos qué vista cargar
        $view = 'view/carrito/carrito.php';

        // 3. Al incluir main.php, la variable $productos ya existe y la vista la puede usar
        include_once 'view/main.php';
    }
}
?>