<?php
//Importamos el modelo de usuario y la base de datos para que todo funcione
include_once 'model/usuario.php';
include_once 'database/database.php';
include_once 'model/usuarioDAO.php';

class usuarioController {

    //Buscamos un usuario específico usando su ID
    public static function getUsuarioByID($id) {
        // Le pasamos el trabajo al DAO para que lo busque en la base de datos
        return usuarioDAO::getUsuarioByID($id);
    }

    //Pedimos la lista completa de todos los usuarios registrados
    public static function getUsuarios() {
        return usuarioDAO::getUsuarios();
    }

    //Aquí controlamos cuando alguien intenta entrar con su cuenta
    public function iniciarSesion() {
        //Arrancamos la sesión si no estaba abierta ya
        if (session_status() === PHP_SESSION_NONE) session_start();

        //Miramos si nos han enviado el email y la contraseña por el formulario (POST)
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['contrasena'])) {
            $email = $_POST['email'];
            $pass  = $_POST['contrasena'];

            //Le preguntamos al DAO si este usuario existe y si la contraseña coincide
            $usuario = usuarioDAO::login($email, $pass);

            if ($usuario) {
                //Si todo está bien, guardamos al usuario en la sesión para que la web lo reconozca
                $_SESSION['usuario'] = $usuario;

                // Opción C: Recordar correo en Cookies
                if (isset($_POST['recordar'])) {
                    // Guardamos la cookie del email por 30 días
                    setcookie('recordar_email', $email, time() + 3600 * 24 * 30, '/');
                } else {
                    // Borramos la cookie si el usuario desmarca la casilla
                    setcookie('recordar_email', '', time() - 3600, '/');
                }

                //Lo mandamos directos a la Home
                header('Location: index.php?controller=home&action=ver_home');
            } else {
                //Si falla, guardamos un mensaje de error para avisar al usuario
                $_SESSION['error_login'] = "Email o contraseña incorrectos.";
                //Lo mandamos de vuelta al login para que lo intente otra vez
                header('Location: index.php?controller=usuario&action=ver_login');
            }
            exit(); //Cortamos aquí para que no se ejecute nada más
        }
    }

    //Lógica para crear una cuenta nueva a los clientes
    public function registrar() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //Creamos un objeto vacío y lo rellenamos con lo que el usuario ha puesto en el formulario
            $nuevo_usuario = new usuario();
            $nuevo_usuario->setNombre($_POST['nombre'] ?? '');
            $nuevo_usuario->setApellido($_POST['apellido'] ?? '');
            $nuevo_usuario->setEmail($_POST['email'] ?? '');
            $nuevo_usuario->setTelefono($_POST['telefono'] ?? '');
            $nuevo_usuario->setRol('cliente'); //A todos los nuevos les ponemos el rol de cliente por defecto
            $nuevo_usuario->setContrasena($_POST['contrasena'] ?? '');

            //Le decimos al DAO que intente guardar este nuevo usuario en la base de datos
            $exito = usuarioDAO::registrarUsuario($nuevo_usuario);
            
            if ($exito) {
                //Si se crea bien, lo mandamos a la pantalla de login para que entre
                header('Location: index.php?controller=usuario&action=ver_login');
            } else {
                //Si falla (por ejemplo, si el email ya existe), avisamos del error
                $_SESSION['error_registro'] = "Error al crear la cuenta. El email ya podría existir.";
                header('Location: index.php?controller=usuario&action=ver_registro');
            }
            exit();
        }
    }

    //Carga la pantalla donde el usuario pone sus datos para entrar
    public function ver_login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $error = null;
        if (isset($_SESSION['error_login'])) {
            $error = $_SESSION['error_login'];
            unset($_SESSION['error_login']);
        }
        
        $pageTitle = 'Iniciar Sesión - Mochistar';
        $view = 'view/login/login.php';
        //Usamos el main.php para que se vea el menú y el pie de página
        include_once 'view/main.php';
    }

    //Carga la pantalla para que los nuevos clientes se apunten
    public function ver_registro() {
        $pageTitle = 'Crear Cuenta - Mochistar';
        $view = 'view/login/registro.php';
        include_once 'view/main.php';
    }

    //Carga la página del perfil del usuario logueado
    public function ver_perfil() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Si no hay sesión, redirigimos al login
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?controller=usuario&action=ver_login');
            exit();
        }
        $pageTitle = 'Mi Perfil - Mochistar';
        $view = 'view/perfil/perfil.php';
        include_once 'view/main.php';
    }

    //Función para cerrar la sesión y salir de la cuenta
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        //Borramos todos los datos de la sesión
        session_destroy();
        //Mandamos al usuario a la página principal de la web
        header('Location: index.php');
        exit();
    }
}