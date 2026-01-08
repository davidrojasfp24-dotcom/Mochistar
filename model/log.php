<?php

class Log {
    // Estas propiedades deben coincidir con las columnas de tu tabla log_admin
    private $id_log;
    private $id_usuario;
    private $accion;
    private $detalle;
    private $tabla_afectada;
    private $fecha;

    // Propiedad auxiliar para guardar el nombre del admin cuando hagamos JOIN
    private $nombre_admin;

    public function __construct() {
        // Constructor vacío para fetch_object
    }

    // --- GETTERS ---
    public function getIdLog() {
        return $this->id_log;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function getAccion() {
        return $this->accion;
    }

    public function getDetalle() {
        return $this->detalle;
    }

    public function getTablaAfectada() {
        return $this->tabla_afectada;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getNombreAdmin() {
        return $this->nombre_admin;
    }

    // --- SETTERS ---
    public function setIdLog($id_log) {
        $this->id_log = $id_log;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function setAccion($accion) {
        $this->accion = $accion;
    }

    public function setDetalle($detalle) {
        $this->detalle = $detalle;
    }

    public function setTablaAfectada($tabla_afectada) {
        $this->tabla_afectada = $tabla_afectada;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setNombreAdmin($nombre_admin) {
        $this->nombre_admin = $nombre_admin;
    }
}
?>