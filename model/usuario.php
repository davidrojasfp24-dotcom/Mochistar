<?php
class usuario {
    private $id;
    private $nombre;
    private $apellido;
    private $email;
    private $telefono;
    private $rol;
    private $contrasena;

    public function __construct()
    {
        
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getRol() {
        return $this->rol;
    }

    public function getContrasena() {
        return $this->contrasena;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setRol($rol) {
        $this->rol = $rol;
    }

    public function setContrasena($contrasena) {
        $this->contrasena = $contrasena;
    }
}
?>