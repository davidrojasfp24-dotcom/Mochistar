<?php
    class apiController {

        public function ver_admin(){
            $view = 'view/panel/admin.php';
            include_once 'view/main.php';
        }

        public function productos(){
            // La ruta correcta desde index.php
            $path = 'api/apiProductos.php';
            
            if (file_exists($path)) {
                require_once $path;
            } else {
                die("Error: No se encuentra el archivo en: " . realpath($path));
            }
            
            // Importante para que no cargue el HTML de main.php
            exit; 
        }

        public function usuarios(){
            $path = 'api/apiUsuarios.php';
            
            if (file_exists($path)) {
                require_once $path;
            } else {
                // Respondemos con JSON si el archivo no existe para no romper el frontend
                header("Content-Type: application/json");
                echo json_encode(['estado' => 'Fallido', 'mensaje' => 'API de usuarios no encontrada']);
            }
            
            // Fundamental para que no se mezcle con el HTML de las vistas
            exit; 
        }
    }
?>