<?php
    include_once 'model/productoDAO.php';

class homeController {

    public function ver_home(){
        //Usamos el DAO para conectarse a la base de datos
        $productos = ProductoDAO::getProductos(); 

        //Definimos qué vista cargar
        $view = 'view/home/home.php';

        //La variable $productos ya existe y la vista la puede usar
        include_once 'view/main.php';
    }
}
?>