<?php
require_once 'database/database.php';

class logDAO {
    /**
     * Obtiene todos los logs de la tabla log_admin
     * Basado en la estructura: id_log, id_usuario, dia/hora, accion, detalle, tabla_afectada
     */
    public static function getLogs() {
        $con = DataBase::connect();
        
        // IMPORTANTE: Ponemos `dia/hora` entre comillas invertidas por el carácter "/"
        $sql = "SELECT id_log, id_usuario, `dia/hora`, accion, detalle, tabla_afectada 
                FROM log_admin 
                ORDER BY id_log DESC";
                
        $stmt = $con->prepare($sql);
        
        if (!$stmt) {
            // Si hay un error en la consulta, esto evitará que la línea 31 explote sin mensaje
            throw new Exception("Error en la consulta SQL: " . $con->error);
        }

        $stmt->execute();
        $results = $stmt->get_result();

        $listaLogs = [];
        // Usamos fetch_assoc para devolver un array asociativo
        while ($fila = $results->fetch_assoc()) {
            $listaLogs[] = $fila;
        }

        $con->close();
        return $listaLogs;
    }
}