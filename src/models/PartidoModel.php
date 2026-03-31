<?php

require_once __DIR__ . '/../config/config.php';

class PartidoModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Devuelve todos los partidos de las fases eliminatorias.
     */
    public function getEnfrentamientosFaseFinal() {
        $sql = "SELECT p.id, p.fase, p.estado, p.goles_local, p.goles_visitante,
                       p.fecha_hora,
                       el.nombre as local_nombre, el.bandera_url as local_bandera,
                       ev.nombre as visitante_nombre, ev.bandera_url as visitante_bandera
                FROM partidos p
                LEFT JOIN equipos el ON p.equipo_local_id = el.id
                LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
                WHERE p.fase IN ('1/16', 'Octavos', 'Cuartos', 'Semis', 'Final')
                ORDER BY p.id ASC";
                
        $stmt = $this->db->query($sql);
        $partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $eliminatorias = [
            '1/16' => [],
            'Octavos' => [],
            'Cuartos' => [],
            'Semis' => [],
            'Final' => []
        ];
        
        foreach ($partidos as $p) {
            // Relleno seguro si aún no se conoce el contendiente
            if (empty($p['local_nombre'])) {
                $p['local_nombre'] = '?';
                $p['local_bandera'] = null;
            }
            if (empty($p['visitante_nombre'])) {
                $p['visitante_nombre'] = '?';
                $p['visitante_bandera'] = null;
            }
            
            $fase = $p['fase'];
            if (isset($eliminatorias[$fase])) {
                $eliminatorias[$fase][] = $p;
            } else {
                $eliminatorias[$fase][] = $p;
            }
        }
        
        return $eliminatorias;
    }
}
