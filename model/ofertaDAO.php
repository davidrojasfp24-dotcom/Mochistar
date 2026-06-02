<?php
include_once 'model/oferta.php';
include_once 'database/database.php';

class ofertaDAO {

    public static function getOfertaByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM oferta WHERE id_oferta = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $oferta = $results->fetch_assoc();
        $con->close();

        return $oferta;
    }

    public static function getOfertas(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM oferta");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaOfertas = [];
        while ($oferta = $results->fetch_assoc()) {
            $listaOfertas[] = $oferta;
        }

        $con->close();
        return $listaOfertas;
    }

    public static function crearOferta($tipo, $descripcion, $descuento) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO oferta (tipo_oferta, descripcion, porcentaje_descuento) VALUES (?, ?, ?)");
        $stmt->bind_param('ssd', $tipo, $descripcion, $descuento);
        $stmt->execute();
        $id = $stmt->insert_id;
        $con->close();
        return $id;
    }

    public static function actualizarOferta($id, $tipo, $descripcion, $descuento) {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE oferta SET tipo_oferta = ?, descripcion = ?, porcentaje_descuento = ? WHERE id_oferta = ?");
        $stmt->bind_param('ssdi', $tipo, $descripcion, $descuento, $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $con->close();
        return $affected;
    }

    public static function eliminarOferta($id) {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM oferta WHERE id_oferta = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $con->close();
        return $affected;
    }

}
?>