<?php
include_once 'model/valoracion.php';
include_once 'database/database.php';

class valoracionDAO {

    public static function getValoracionByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM valoracion WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $valoracion = $results->fetch_object('valoracion');
        $con->close();

        return $valoracion;
    }

    public static function getValoraciones(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM valoracion");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaValoraciones = [];
        while ($valoracion = $results->fetch_object('valoracion')) {
            $listaValoraciones[] = $valoracion;
        }

        $con->close();
        return $listaValoraciones;
    }

}
?>