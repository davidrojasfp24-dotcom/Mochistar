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

    // fetch_object() asigna propiedades públicas por nombre de columna.
    // Como nuestras propiedades son privadas, usamos __set para capturarlas
    // y mapear los nombres de columna de la BD a nuestras propiedades internas.
    public function __set($name, $value) {
        switch ($name) {
            case 'id_usuario':  $this->id         = $value; break;
            case 'contrasena':  $this->contrasena  = $value; break;
            case 'nombre':      $this->nombre      = $value; break;
            case 'apellido':    $this->apellido    = $value; break;
            case 'email':       $this->email       = $value; break;
            case 'telefono':    $this->telefono    = $value; break;
            case 'rol':         $this->rol         = $value; break;
        }
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