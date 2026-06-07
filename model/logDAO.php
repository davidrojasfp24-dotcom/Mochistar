<?php
require_once 'database/database.php';

class logDAO {
    /**
     * Obtiene todos los logs de la tabla logs
     * Basado en la estructura: log_id, usuario_id, mensaje, fecha
     */
    public static function getLogs() {
        $con = DataBase::connect();
        
        $sql = "SELECT log_id, usuario_id, mensaje, fecha 
                FROM logs 
                ORDER BY log_id DESC";
                
        $stmt = $con->prepare($sql);
        
        if (!$stmt) {
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
?>