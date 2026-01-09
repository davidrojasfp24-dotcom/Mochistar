<?php
//Importamos la conexión a la base de datos y el modelo para manejar usuarios
require_once 'database/database.php';
require_once 'model/usuario.php';

class usuarioDAO {

    //Buscamos un usuario específico usando su ID (id_usuario)
    public static function getUsuarioByID($id){
        $con = DataBase::connect();
        //Preparamos la consulta para que sea segura
        $stmt = $con->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        //Guardamos los datos del usuario en un formato fácil de usar
        $usuario = $results->fetch_assoc(); 
        $con->close();

        return $usuario;
    }

    //Sacamos la lista de todos los usuarios registrados, de más nuevos a más antiguos
    public static function getUsuarios(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario ORDER BY id_usuario DESC");
        $stmt->execute();
        $results = $stmt->get_result();

        //Vamos guardando cada usuario encontrado en una lista
        $listaUsuarios = [];
        while ($usuario = $results->fetch_assoc()) {
            $listaUsuarios[] = $usuario;
        }

        $con->close();
        return $listaUsuarios;
    }

    //Función para que el administrador cree nuevos usuarios desde su panel
    public function crear($nombre, $apellido, $email, $telefono, $rol, $password, $id_admin) {
        $con = DataBase::connect();
        
        //Ciframos la contraseña para que nadie pueda verla, ni siquiera nosotros
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $con->prepare("INSERT INTO usuario (nombre, apellido, email, telefono, rol, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $apellido, $email, $telefono, $rol, $hash);
        
        $crear = $stmt->execute();

        //Si se crea bien, registramos en el historial qué admin ha creado esta cuenta
        if ($crear) {
            $this->registrarLog($id_admin, 'INSERT', "Creado usuario: $email", 'usuario');
        }

        $con->close();
        return $crear;
    }

    //Modificamos los datos de un usuario (nombre, email, rol, etc.)
    public function modificar($id, $nombre, $apellido, $email, $telefono, $rol, $id_admin) {
        $con = DataBase::connect();
        $stmt = $con->prepare("UPDATE usuario SET nombre=?, apellido=?, email=?, telefono=?, rol=? WHERE id_usuario=?");
        
        $stmt->bind_param("sssssi", $nombre, $apellido, $email, $telefono, $rol, $id);
        
        $success = $stmt->execute();

        //Si se modifica con éxito, dejamos rastro en el registro de seguridad
        if ($success) {
            $this->registrarLog($id_admin, 'UPDATE', "Modificado usuario ID: $id", 'usuario');
        }
        $con->close();
        return $success;
    }

    //Borramos un usuario de la base de datos
    public function eliminar($id, $id_admin) {
        $con = DataBase::connect();
        $stmt = $con->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        
        $success = $stmt->execute();

        //Dejamos anotado quién ha sido el administrador que ha borrado al usuario
        if ($success) {
            $this->registrarLog($id_admin, 'DELETE', "Eliminado usuario ID: $id", 'usuario');
        }
        $con->close();
        return $success;
    }

    //La llave maestra: Comprobamos si el email y la contraseña son correctos para entrar
    public static function login($email, $password){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $results = $stmt->get_result();
        
        $usuario_logeado = null;

        //Si encontramos un usuario con ese email...
        if ($results->num_rows == 1) {
            //Convertimos el resultado en un objeto de tipo 'usuario' para usar sus funciones
            $usuario = $results->fetch_object('usuario');
            
            //Comparamos la contraseña escrita con la que tenemos cifrada en la base de datos
            if (password_verify($password, $usuario->getContrasena())) {
                $usuario_logeado = $usuario; 
            }
        } 
        
        $con->close();
        return $usuario_logeado; 
    }

    //El diario de seguridad: Registra qué hace cada administrador para tenerlo todo controlado
    private function registrarLog($id_user, $accion, $detalle, $tabla) {
        $con = DataBase::connect();
        $stmt = $con->prepare("INSERT INTO log_admin (id_usuario, accion, detalle, tabla_afectada) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_user, $accion, $detalle, $tabla);
        $stmt->execute();
        $con->close();
    }

    //Registro rápido para los clientes que se apuntan por primera vez a la web
    public static function registrarUsuario($usuario) {
        $con = DataBase::connect();
        
        //Cogemos la contraseña que ha puesto el cliente y la ciframos por seguridad
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