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
    }
?>