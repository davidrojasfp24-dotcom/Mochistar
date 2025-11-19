<?php
class producto {
    private $id;
    private $imagen;
    private $cantidad;
    private $descripcion;
    private $nombre;
    private $precio_unidad;
    private $id_oferta;

    public function __construct()
    {
        
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getImagen() {
        return $this->imagen;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getPrecioUnidad() {
        return $this->precio_unidad;
    }

    public function getIdOferta() {
        return $this->id_oferta;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setImagen($imagen) {
        $this->imagen = $imagen;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setNombre($nombre) {
        $nombre = trim($nombre); 
        $this->nombre = $nombre;
    }

    public function setPrecioUnidad($precio_unidad) {
        $this->precio_unidad = $precio_unidad;
    }

    public function setIdOferta($id_oferta) {
        $this->id_oferta = $id_oferta;
    }
}
?>