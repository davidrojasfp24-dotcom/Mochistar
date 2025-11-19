<?php
class pedido {
    private $id;
    private $estado;
    private $fecha;
    private $precio;
    private $id_usuario;

    public function __construct()
    {
        
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }
}
