<?php

class Log {
    private $log_id;
    private $usuario_id;
    private $mensaje;
    private $fecha;

    public function __construct() {
        // Constructor vacío para fetch_object u otros usos
    }

    // --- GETTERS ---
    public function getLogId() {
        return $this->log_id;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

    public function getMensaje() {
        return $this->mensaje;
    }

    public function getFecha() {
        return $this->fecha;
    }

    // --- SETTERS ---
    public function setLogId($log_id) {
        $this->log_id = $log_id;
    }

    public function setUsuarioId($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }
}
?>