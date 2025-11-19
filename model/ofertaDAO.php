<?php
include_once 'model/oferta.php';
include_once 'database/database.php';

class ofertaDAO {

    public static function getOfertaByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM oferta WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $oferta = $results->fetch_object('Oferta');
        $con->close();

        return $oferta;
    }

    public static function getOfertas(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM oferta");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaOfertas = [];
        while ($oferta = $results->fetch_object('Oferta')) {
            $listaOfertas[] = $oferta;
        }

        $con->close();
        return $listaOfertas;
    }

}
?>