<?php
require_once 'database/database.php';
require_once 'model/usuario.php';

class usuarioDAO {

    /**
     * Obtiene un usuario específico por su ID_USUARIO
     */
    public static function getUsuarioByID($id){
        $con = DataBase::connect();
        // Corregido: id -> id_usuario
        $stmt = $con->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $usuario = $results->fetch_assoc(); 
        $con->close();

        return $usuario;
    }

    /**
     * Obtiene todos los usuarios ordenados por ID_USUARIO
     */
    public static function getUsuarios(){
        $con = DataBase::connect();
        // Corregido: id -> id_usuario
        $stmt = $con->prepare("SELECT * FROM usuario ORDER BY id_usuario DESC");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaUsuarios = [];
        while ($usuario = $results->fetch_assoc()) {
            $listaUsuarios[] = $usuario;
        }

        $con->close();
        return $listaUsuarios;
    }

    /**
     * Inserta un nuevo usuario y registra la acción en log_admin
     */
    public function crear($nombre, $apellido, $email, $telefono, $rol, $password, $id_admin) {
        $con = DataBase::connect();
        
        // Hasheamos la contraseña para seguridad
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $con->prepare("INSERT INTO usuario (nombre, apellido, email, telefono, rol, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $apellido, $email, $telefono, $rol, $hash);
        
        $crear = $stmt->execute();
        if ($crear) {
            $this->registrarLog($id_admin, 'INSERT', "Creado usuario: $email", 'usuario');
        }

        $con->close();
        return $crear;
    }

    /**
     * Actualiza un usuario existente
     */
    public function modificar($id, $nombre, $apellido, $email, $telefono, $rol, $id_admin) {
        $con = DataBase::connect();
        // Corregido: id -> id_usuario
        $stmt = $con->prepare("UPDATE usuario SET nombre=?, apellido=?, email=?, telefono=?, rol=? WHERE id_usuario=?");
        
        $stmt->bind_param("sssssi", $nombre, $apellido, $email, $telefono, $rol, $id);
        
        $success = $stmt->execute();
        if ($success) {
            $this->registrarLog($id_admin, 'UPDATE', "Modificado usuario ID: $id", 'usuario');
        }
        $con->close();
        return $success;
    }

    /**
     * Elimina un usuario por ID_USUARIO
     */
    public function eliminar($id, $id_admin) {
        $con = DataBase::connect();
        // Corregido: id -> id_usuario
        $stmt = $con->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        
        $success = $stmt->execute();
        if ($success) {
            $this->registrarLog($id_admin, 'DELETE', "Eliminado usuario ID: $id", 'usuario');
        }
        $con->close();
        return $success;
    }

    /**
     * Función para el Login: usa fetch_object para cargar métodos del modelo usuario
     */
    public static function login($email, $password){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $results = $stmt->get_result();
        
        $usuario_logeado = null;

        if ($results->num_rows == 1) {
            $usuario = $results->fetch_object('usuario');
            if (password_verify($password, $usuario->getContrasena())) {
                $usuario_logeado = $usuario; 
            }
        } 
        
        $con->close();
        return $usuario_logeado; 
    }

    /**
     * Auditoría de acciones de administrador
     */
    private function registrarLog($id_user, $accion, $detalle, $tabla) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_user, $accion, $detalle, $tabla);
        $stmt->execute();
        $con->close();
    }

    /**
     * Registro rápido de usuario (clientes)
     */
    public static function registrarUsuario($usuario) {
        $con = DataBase::connect();
        
        $password_plana = $usuario->getContrasena(); 
        $hash_contrasena = password_hash($password_plana, PASSWORD_DEFAULT);
        
        $stmt = $con->prepare("INSERT INTO usuario (nombre, apellido, email, telefono, rol, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            'ssssss', 
            $usuario->getNombre(), 
            $usuario->getApellido(), 
            $usuario->getEmail(), 
            $usuario->getTelefono(), 
            $usuario->getRol(), 
            $hash_contrasena
        );
        
        $result = $stmt->execute();
        $con->close();
        
        return $result;
    }
}