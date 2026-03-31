<?php

class ApiController {
    
    /**
     * Orquesta la petición recibida y despacha la respuesta JSON
     */
    public function handleRequest() {
        // Aseguramos headers JSON
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'get_grupos':
                $this->getGrupos();
                break;
            case 'get_eliminatorias':
                $this->getEliminatorias();
                break;
            default:
                $this->sendResponse(['error' => 'Acción no especificada o inválida'], 400);
                break;
        }
    }

    /**
     * Lógica para obtener grupos
     */
    private function getGrupos() {
        $model = new EquipoModel();
        
        try {
            $data = $model->getClasificacionPorGrupos();
            $this->sendResponse(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener los grupos', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Lógica para obtener eliminatorias
     */
    private function getEliminatorias() {
        $model = new PartidoModel();
        
        try {
            $data = $model->getEnfrentamientosFaseFinal();
            $this->sendResponse(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al obtener las eliminatorias', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Utility para estandarizar la salida JSON
     */
    private function sendResponse($payload, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($payload);
        exit;
    }
}
