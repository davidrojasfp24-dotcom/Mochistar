<?php
include_once 'model/productoDAO.php';

class productoController {
    
    public function ver_carta(){
        // 1. Aquí es donde se obtienen los productos
        // Usamos el DAO que ya sabe cómo conectarse a la base de datos
        $productos = ProductoDAO::getProductos(); 

        // 2. Definimos qué vista cargar
        $view = 'view/carta/carta.php';

        // 3. Al incluir main.php, la variable $productos ya existe y la vista la puede usar
        include_once 'view/main.php';
    }
}