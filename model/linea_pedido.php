<?php
class lineaPedido {
    private $id;
    private $precio_unidad;
    private $cantidad;
    private $porcentaje_descuento;
    private $id_pedido;
    private $id_producto;

    public function __construct()
    {
        
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getPrecioUnidad() {
        return $this->precio_unidad;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function getPorcentajeDescuento() {
        return $this->porcentaje_descuento;
    }

    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function getIdProducto() {
        return $this->id_producto;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setPrecioUnidad($precio_unidad) {
        $this->precio_unidad = $precio_unidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function setPorcentajeDescuento($porcentaje_descuento) {
        $this->porcentaje_descuento = $porcentaje_descuento;
    }

    public function setIdPedido($id_pedido) {
        $this->id_pedido = $id_pedido;
    }

    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }
}
?>