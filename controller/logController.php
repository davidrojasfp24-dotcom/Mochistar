<?php
require_once 'model/logDAO.php';

class logController {

    /**
     * Método principal para obtener y mostrar los logs.
     * Este método será llamado vía AJAX desde el panel admin.
     */
    public function index() {
        // 1. Iniciar sesión si no existe
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Seguridad: Verificar que sea Administrador
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']->getRol() !== 'admin') {
            echo json_encode([
                'estado' => 'Error',
                'mensaje' => 'No tienes permisos para ver el historial.'
            ]);
            return;
        }

        // 3. Obtener los datos del DAO
        try {
            $listaLogs = logDAO::getLogs();

            // 4. Preparar los datos para JSON
            // Convertimos los objetos en un array simple para que JavaScript lo entienda perfectamente
            $dataResponse = [];
            foreach ($listaLogs as $l) {
                $dataResponse[] = [
                    'id_log'         => $l->getIdLog(),
                    'nombre_admin'   => $l->getNombreAdmin() ?? 'Sistema', // Si el admin fue borrado, pone 'Sistema'
                    'accion'         => $l->getAccion(),
                    'detalle'        => $l->getDetalle(),
                    'tabla_afectada' => $l->getTablaAfectada(),
                    'fecha'          => $l->getFecha()
                ];
            }

            // 5. Enviar la respuesta
            header('Content-Type: application/json');
            echo json_encode($dataResponse);

        } catch (Exception $e) {
            echo json_encode([
                'estado' => 'Error',
                'mensaje' => 'Error al obtener los logs: ' . $e->getMessage()
            ]);
        }
    }
}