<?php
class apiController {

    public function ver_admin(){
        $view = 'view/panel/admin.php';
        include_once 'view/main.php';
    }

    public function productos(){
        $path = 'api/apiProductos.php';
        if (file_exists($path)) {
            require_once $path;
        } else {
            die("Error: No se encuentra el archivo en: " . realpath($path));
        }
        exit; 
    }

    public function usuarios(){
        $path = 'api/apiUsuario.php'; 
        if (file_exists($path)) {
            require_once $path;
        } else {
            header("Content-Type: application/json");
            echo json_encode(['estado' => 'Fallido', 'mensaje' => 'API de usuarios no encontrada']);
        }
        exit; 
    }

    /**
     * NUEVO: API de Pedidos
     */
    public function pedidos(){
        $path = 'api/apiPedido.php';
        if (file_exists($path)) {
            require_once $path;
        } else {
            header("Content-Type: application/json");
            echo json_encode(['estado' => 'Error', 'mensaje' => 'API de pedidos no encontrada']);
        }
        exit; 
    }

    /**
     * NUEVO: API de Historial (Logs)
     */
    public function logs(){
        $path = 'api/apiLog.php';
        if (file_exists($path)) {
            require_once $path;
        } else {
            header("Content-Type: application/json");
            echo json_encode(['estado' => 'Error', 'mensaje' => 'API de logs no encontrada']);
        }
        exit; 
    }
}
?>