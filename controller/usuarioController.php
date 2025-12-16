<?php
include_once 'model/usuario.php';
include_once 'database/database.php';
include_once 'model/usuarioDAO.php';

class usuarioController{

    public static function getUsuarioByID($id)
    {
        $con = DataBase::connect();
        $stmt = $con->prepare("SELECT * FROM usuario WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $results = $stmt->get_result();

        $usuario = $results->fetch_object('usuario');
        $con->close();

        return $usuario;
    }

    public static function getUsuarios()
    {
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

    public function iniciarSesion()
    {

        session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['contrasena'])) {

            $email_ingresado = $_POST['email'];
            $contrasena_ingresada = $_POST['contrasena'];

            $usuario = usuarioDAO::login($email_ingresado, $contrasena_ingresada);

            if ($usuario) {
                $_SESSION['usuario'] = $usuario;
                header('Location: index.php?controller=home&action=ver_home');
                exit();
            } else {
                $_SESSION['error_login'] = "Email o contraseña incorrectos.";
                header('Location: index.php?controller=usuario&action=ver_login');
                exit();
            }
        }
    }

    public function ver_login(){
        $view = 'view/login/login.php';
        include_once 'view/main.php';
    }

    // En usuarioController.php
public function registrar() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // 1. Crear un nuevo objeto usuario y asignar datos (asume que los names del formulario son correctos)
        $nuevo_usuario = new usuario();
        $nuevo_usuario->setNombre($_POST['nombre'] ?? '');
        $nuevo_usuario->setApellido($_POST['apellido'] ?? '');
        $nuevo_usuario->setEmail($_POST['email'] ?? '');
        $nuevo_usuario->setTelefono($_POST['telefono'] ?? null); // Telefono puede ser opcional
        $nuevo_usuario->setContrasena($_POST['contrasena'] ?? ''); // ¡Guardamos la contraseña plana temporalmente!
        
        // Asignar un rol por defecto (ajusta según tu lógica)
        $nuevo_usuario->setRol('user'); 
        
        // 2. Llamar al DAO para realizar la inserción
        $exito = usuarioDAO::registrarUsuario($nuevo_usuario);
        
        if ($exito) {
            // Registro exitoso, redirigir al login
            header('Location: index.php?controller=usuario&action=ver_login');
            exit();
        } else {
            // Fallo en el registro (ej. email ya existe, fallo de DB)
            // Aquí deberías manejar y mostrar un mensaje de error
            $_SESSION['error_registro'] = "Error al registrar usuario. El email podría estar en uso.";
            header('Location: index.php?controller=usuario&action=ver_registro');
            exit();
        }
    }
    // Si no es POST, redirigir
    header('Location: index.php?controller=usuario&action=ver_registro');
    exit();
}

    public function ver_registro() {
    $view = 'view/login/registro.php';
    include_once 'view/main.php';
}
}
