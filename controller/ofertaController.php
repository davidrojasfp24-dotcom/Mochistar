<?php
include_once 'model/ofertaDAO.php';

class ofertaController {

    public function show(){
        $view = 'view/oferta/show.php';   // Llamamos a la vista
        $idoferta = $_GET['idoferta'];    // Recibimos el ID por GET
        $oferta = ofertaDAO::getOfertaByID($idoferta);
        include_once 'view/main.php';     // Cargamos el layout principal
    }

    public function index(){
        $view = 'view/oferta/index.php';  // Llamamos a la vista
        $listaOfertas = ofertaDAO::getOfertas();
        include_once 'view/main.php';     // Cargamos el layout principal
    }
}
?>