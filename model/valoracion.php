<?php
class Valoracion {
    private $id;
    private $tipo_valoracion;
    private $time_stamp;
    private $valoracion;
    private $comentario;
    private $id_usuario;
    private $id_producto;

    public function __construct()
    {
        
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTipoValoracion() {
        return $this->tipo_valoracion;
    }

    public function getTimeStamp() {
        return $this->time_stamp;
    }

    public function getValoracion() {
        return $this->valoracion;
    }

    public function getComentario() {
        return $this->comentario;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getIdProducto() {
        return $this->id_producto;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTipoValoracion($tipo_valoracion) {
        $this->tipo_valoracion = $tipo_valoracion;
    }

    public function setTimeStamp($time_stamp) {
        $this->time_stamp = $time_stamp;
    }

    public function setValoracion($valoracion) {
        $this->valoracion = $valoracion;
    }

    public function setComentario($comentario) {
        $this->comentario = $comentario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }
}
?>