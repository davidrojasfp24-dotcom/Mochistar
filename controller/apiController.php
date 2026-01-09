<?php
class apiController
{

    public function ver_admin()
    {
        //Ponemos lo que queremos ver en la vista
        $view = 'view/panel/admin.php';
        //La variable ya existe y la vista la puede usar
        include_once 'view/main.php';
    }

    public function productos()
    {
        //Guardamos la dirección de nuestra API
        $path = 'api/apiProductos.php';
        //Comprobamos que el archivo esté en su sitio para que la web no se rompa al cargarlo
        if (file_exists($path)) {
            //Si el archivo existe lo llamamos
            require_once $path;
        } else {
            //Si no lo encuentra, paramos todo y avisamos de qué ruta exacta está fallando
            die("Error: No se encuentra el archivo en: " . realpath($path));
        }
        // Cortamos la ejecución aquí para que no se pinte nada de HTML después de los datos JSON
        exit;
    }

    public function usuarios()
    {
        //Guardamos la dirección de nuestra API
        $path = 'api/apiUsuario.php';
        // Comprobamos que el archivo esté en su sitio para que la web no se rompa al cargarlo
        if (file_exists($path)) {
            //Si el archivo existe lo llamamos
            require_once $path;
        } else {
            //Si no lo encuentra, avisamos al navegador que enviaremos un JSON de error
            header("Content-Type: application/json");
            //Enviamos el mensaje de fallo para que el JavaScript sepa que la API no responde
            echo json_encode(['estado' => 'Fallido', 'mensaje' => 'API de usuarios no encontrada']);
        }
        exit;
    }

    // public function pedidos(){
    //     $path = 'api/apiPedido.php';
    //     if (file_exists($path)) {
    //         require_once $path;
    //     } else {
    //         header("Content-Type: application/json");
    //         echo json_encode(['estado' => 'Error', 'mensaje' => 'API de pedidos no encontrada']);
    //     }
    //     exit; 
    // }

    // public function logs(){
    //     $path = 'api/apiLog.php';
    //     if (file_exists($path)) {
    //         require_once $path;
    //     } else {
    //         header("Content-Type: application/json");
    //         echo json_encode(['estado' => 'Error', 'mensaje' => 'API de logs no encontrada']);
    //     }
    //     exit; 
    // }
}
