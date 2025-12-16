<?php
include_once 'model/usuario.php';
include_once 'database/database.php';

class usuarioDAO {

    public static function getUsuarioByID($id){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $usuario = $results->fetch_object('usuario');
        $con->close();

        return $usuario;
    }

    public static function getUsuarios(){
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario");
        $stmt->execute();
        $results = $stmt->get_result();

        $listaUsuarios = [];
        while ($usuario = $results->fetch_object('usuario')) {
            $listaUsuarios[] = $usuario;
        }

        $con->close();
        return $listaUsuarios;
    }


    public static function login($email, $password){
        $con = DataBase::connect();

        // 1. Consulta para obtener al usuario por email
        $stmt = $con->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $results = $stmt->get_result();
        
        $usuario_logeado = null; // Inicializamos a NULL

        if ($results->num_rows == 1) {
            // 2. Mapeo del resultado a un objeto de la clase 'usuario'
            // Esto carga el valor de la columna 'contrasena' en $usuario->contrasena
            $usuario = $results->fetch_object('usuario');

            // 3. Verificación de la Contraseña
            // password_verify() compara el password plano con el hash almacenado 
            $hash_almacenado = $usuario->getContrasena(); // Usamos el Getter
            
            if (password_verify($password, $hash_almacenado)) {
                $usuario_logeado = $usuario; // Login exitoso
            }
        } 
        
        // 4. Cierre y Retorno
        $con->close();
        return $usuario_logeado; 
    }

    public static function registrarUsuario($usuario) {
        $con = DataBase::connect();
        
        // 1. OBTENER Y HASHEAR LA CONTRASEÑA
        // Usamos la contraseña del objeto, pero primero la hasheamos.
        // Asumimos que setContrasena en la clase usuario NO hashea.
        $password_plana = $usuario->getContrasena(); 
        $hash_contrasena = password_hash($password_plana, PASSWORD_DEFAULT); // ¡Seguridad!
        
        // 2. PREPARAR LA CONSULTA
        // Asegúrate de que los campos coincidan con tu tabla: id_usuario, nombre, apellido, email, telefono, rol, contrasena
        $stmt = $con->prepare("INSERT INTO usuario (nombre, apellido, email, telefono, rol, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Los tipos son: s (string) para todos los campos de texto/varchar
        $stmt->bind_param(
            'ssssss', 
            $usuario->getNombre(), 
            $usuario->getApellido(), 
            $usuario->getEmail(), 
            $usuario->getTelefono(), 
            $usuario->getRol(), 
            $hash_contrasena // Insertamos el HASH, no el valor plano
        );
        
        // 3. EJECUTAR
        $result = $stmt->execute();
        $con->close();
        
        return $result; // Devuelve true si la inserción fue exitosa
    }


}
