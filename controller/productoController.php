<?php
include_once 'model/productoDAO.php';

class productoController {
    
    public function ver_carta(){
        // 1. Aquí es donde se obtienen los productos
        $productos = ProductoDAO::getProductos(); 

        // 2. Título dinámico
        $pageTitle = 'Nuestra Carta';

        // 3. Definimos qué vista cargar
        $view = 'view/carta/carta.php';

        // 4. Incluir el layout base
        include_once 'view/main.php';
    }

    public function ver_detalle(){
        // 1. Obtener el ID del producto de la URL
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // 2. Obtener el producto
        $producto = ProductoDAO::getProductoByID($id);
        
        if (!$producto) {
            header("Location: index.php?controller=producto&action=ver_carta");
            exit();
        }
        
        // 3. Obtener productos relacionados (excluyendo el actual, máximo 3)
        $todos = ProductoDAO::getProductos();
        $relacionados = [];
        foreach ($todos as $p) {
            if (intval($p['id_producto']) !== intval($producto['id_producto'])) {
                $relacionados[] = $p;
                if (count($relacionados) >= 3) {
                    break;
                }
            }
        }
        
        // 4. Título dinámico
        $pageTitle = $producto['nombre'] . ' - Mochistar';
        
        // 5. Definimos qué vista cargar
        $view = 'view/detalle/detalle.php';
        
        // 6. Cargar layout
        include_once 'view/main.php';
    }
}