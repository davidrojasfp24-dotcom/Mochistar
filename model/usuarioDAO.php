<?php
include_once 'model/usuario.php';
include_once 'database/database.php';

class usuarioDAO {

    public static function getUsuarioByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $usuario = $results->fetch_object('usuario');
        $con->close();

        return $usuario;
    }

    public static function getUsuarios(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaUsuarios = [];
        while ($usuario = $results->fetch_object('usuario')) {
            $listaUsuarios[] = $usuario;
        }

        $con->close();
        return $listaUsuarios;
    }

}
