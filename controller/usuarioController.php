<?php
include_once 'model/usuario.php';
include_once 'database/database.php';
include_once 'model/usuarioDAO.php';

class usuarioController {

    /**
     * Obtiene un usuario por ID delegando en el DAO
     */
    public static function getUsuarioByID($id) {
        // Delegamos la responsabilidad al DAO
        return usuarioDAO::getUsuarioByID($id);
    }

    /**
     * Obtiene todos los usuarios delegando en el DAO
     */
    public static function getUsuarios() {
        return usuarioDAO::getUsuarios();
    }

    /**
     * Lógica de inicio de sesión
     */
    public function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['contrasena'])) {
            $email = $_POST['email'];
            $pass  = $_POST['contrasena'];

            // Llamamos al método estático del DAO
            $usuario = usuarioDAO::login($email, $pass);

            if ($usuario) {
                // Guardamos el objeto usuario en la sesión
                $_SESSION['usuario'] = $usuario;
                header('Location: index.php?controller=home&action=ver_home');
            } else {
                $_SESSION['error_login'] = "Email o contraseña incorrectos.";
                header('Location: index.php?controller=usuario&action=ver_login');
            }
            exit();
        }
    }

    /**
     * Lógica de registro de nuevos clientes
     */
    public function registrar() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // 1. Creamos el objeto con los datos del formulario
            $nuevo_usuario = new usuario();
            $nuevo_usuario->setNombre($_POST['nombre'] ?? '');
            $nuevo_usuario->setApellido($_POST['apellido'] ?? '');
            $nuevo_usuario->setEmail($_POST['email'] ?? '');
            $nuevo_usuario->setTelefono($_POST['telefono'] ?? '');
            $nuevo_usuario->setRol('cliente'); // Rol por defecto
            $nuevo_usuario->setContrasena($_POST['contrasena'] ?? '');

            // 2. Intentamos registrar a través del DAO
            $exito = usuarioDAO::registrarUsuario($nuevo_usuario);
            
            if ($exito) {
                header('Location: index.php?controller=usuario&action=ver_login');
            } else {
                $_SESSION['error_registro'] = "Error al crear la cuenta. El email ya podría existir.";
                header('Location: index.php?controller=usuario&action=ver_registro');
            }
            exit();
        }
    }

    /**
     * Carga la vista de Login
     */
    public function ver_login() {
        $view = 'view/login/login.php';
        include_once 'view/main.php';
    }

    /**
     * Carga la vista de Registro
     */
    public function ver_registro() {
        $view = 'view/login/registro.php';
        include_once 'view/main.php';
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: index.php');
        exit();
    }
}