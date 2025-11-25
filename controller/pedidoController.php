<?php
include_once 'model/pedido.php';
include_once 'database/database.php';

class pedidoDAO {

    public static function getPedidoByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM pedido WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $pedido = $results->fetch_object('pedido');
        $con->close();

        return $pedido;
    }

    public static function getPedidos(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM pedido");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaPedidos = [];
        while ($pedido = $results->fetch_object('pedido')) {
            $listaPedidos[] = $pedido;
        }

        $con->close();
        return $listaPedidos;
    }

}
?>