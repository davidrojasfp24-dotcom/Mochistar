<?php
class Oferta {
    private $id;
    private $tipo_oferta;
    private $descripcion;
    private $porcentaje_descuento;

    public function __construct()
    {
        
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTipoOferta() {
        return $this->tipo_oferta;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getPorcentajeDescuento() {
        return $this->porcentaje_descuento;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setTipoOferta($tipo_oferta) {
        $this->tipo_oferta = $tipo_oferta;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setPorcentajeDescuento($porcentaje_descuento) {
        $this->porcentaje_descuento = $porcentaje_descuento;
    }
}
?>