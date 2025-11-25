<?php
include_once 'model/lineaPedidoDAO.php';

class lineaPedidoController {

    public function show(){
        $view = 'view/linea_pedido/show.php';
        $idlinea = $_GET['idlinea'];
        $linea = lineaPedidoDAO::getLineaPedidoByID($idlinea);
        include_once 'view/main.php';
    }

    public function index(){
        $view = 'view/linea_pedido/index.php';
        $listaLineas = lineaPedidoDAO::getLineasPedido();
        include_once 'view/main.php';
    }
}
?>