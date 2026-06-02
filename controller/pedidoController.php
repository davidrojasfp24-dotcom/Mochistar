<?php
require_once 'model/pedidoDAO.php';

class pedidoController {

    /**
     * Lista todos los pedidos para el panel de administración
     */
    public function index() {
        // 1. Iniciar sesión y seguridad
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
            echo json_encode(['estado' => 'Error', 'mensaje' => 'No autorizado']);
            return;
        }

        try {
            // 2. Obtener datos del DAO
            $listaPedidos = pedidoDAO::getPedidos();
            $dataResponse = [];

            foreach ($listaPedidos as $p) {
                $dataResponse[] = [
                    'id'         => $p->getId(),
                    'estado'     => $p->getEstado(),
                    'fecha'      => date("d/m/Y H:i", strtotime($p->getFecha())),
                    'precio'     => $p->getPrecio(),
                    'id_usuario' => $p->getIdUsuario()
                ];
            }

            // 3. Respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($dataResponse);

        } catch (Exception $e) {
            echo json_encode(['estado' => 'Error', 'mensaje' => $e->getMessage()]);
        }
    }

    /**
     * Cambia el estado de un pedido (Llamado vía POST/AJAX)
     */
    public function actualizarEstado() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Recibir datos (asumiendo JSON)
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        if (isset($datos['id_pedido']) && isset($datos['nuevo_estado'])) {
            $dao = new pedidoDAO();
            $id_admin = $_SESSION['usuario']->getId(); // Quién hace el cambio

            $res = $dao->modificarEstado($datos['id_pedido'], $datos['nuevo_estado'], $id_admin);

            echo json_encode([
                'estado' => $res ? 'Exito' : 'Error',
                'mensaje' => $res ? 'Estado actualizado correctamente' : 'No se pudo actualizar'
            ]);
        }
    }
}