<?php
include_once 'model/lineaPedido.php';
include_once 'database/database.php';


class lineaPedidoDAO {

    public static function getLineaPedidoByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM linea_pedido WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $lineaPedido = $results->fetch_object('LineaPedido');
        $con->close();

        return $lineaPedido;
    }

    public static function getLineasPedido(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM linea_pedido");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaLineas = [];
        while ($linea = $results->fetch_object('LineaPedido')) {
            $listaLineas[] = $linea;
        }

        $con->close();
        return $listaLineas;
    }

}
?>