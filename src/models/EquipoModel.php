<?php

require_once __DIR__ . '/../config/config.php';

class EquipoModel {
    private $db;

    public function __construct() {
        // Dependency Injection style using Singleton
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Devuelve los equipos agrupados por su grupo (A, B...) 
     * ordenados por puntos de mayor a menor.
     */
    public function getClasificacionPorGrupos() {
        // Obtenemos todos los equipos
        $sql = "SELECT id, nombre, bandera_url, grupo, puntos_totales
                FROM equipos 
                ORDER BY grupo ASC, puntos_totales DESC";
        $stmt = $this->db->query($sql);
        $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Preparamos consulta para los 3 partidos de la fase de grupos por equipo
        $sqlPartidos = "SELECT 
                            p.fecha_hora,
                            IF(p.equipo_local_id = :eq_id, ev.nombre, el.nombre) as rival_nombre,
                            IF(p.equipo_local_id = :eq_id, 'Local', 'Visitante') as condicion
                        FROM partidos p
                        JOIN equipos el ON p.equipo_local_id = el.id
                        JOIN equipos ev ON p.equipo_visitante_id = ev.id
                        WHERE (p.equipo_local_id = :eq_id OR p.equipo_visitante_id = :eq_id)
                        AND p.fase = 'grupo'
                        ORDER BY p.fecha_hora ASC";
        $stmtPartidos = $this->db->prepare($sqlPartidos);
        
        $grupos = [];
        foreach ($equipos as $equipo) {
            $stmtPartidos->execute(['eq_id' => $equipo['id']]);
            $equipo['partidos'] = $stmtPartidos->fetchAll(PDO::FETCH_ASSOC);
            $grupos[$equipo['grupo']][] = $equipo;
        }
        
        return $grupos;
    }
}
