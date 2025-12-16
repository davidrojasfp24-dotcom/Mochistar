<?php
include_once 'model/ofertaDAO.php';

class ofertaController {

    public function show(){
        $view = 'view/oferta/show.php';
        $idoferta = $_GET['idoferta'];
        $oferta = ofertaDAO::getOfertaByID($idoferta);
        include_once 'view/main.php';
    }

    public function index(){
        $view = 'view/oferta/index.php';
        $listaOfertas = ofertaDAO::getOfertas();
        include_once 'view/main.php';
    }
}
?>