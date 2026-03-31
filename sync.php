<?php
/**
 * =========================================================================
 * BRACKET26 - SINCRONIZADOR AUTOMÁTICO DE PARTIDOS (MODO PRODUCCIÓN)
 * =========================================================================
 * 
 * Este script automatiza la extracción de resultados y actualizaciones 
 * reales desde API-Football hacia la base de datos de Bracket26.
 * 
 * PROTECCIÓN: 
 * Requiere ejecución vía CLI o token de seguridad ?token=... en HTTP.
 */
// 1. Carga de configuración y Capa de Seguridad
require_once __DIR__ . '/src/config/config.php';
$token_esperado = CRON_TOKEN;
$is_cli = (php_sapi_name() === 'cli');
$token_recibido = isset($_GET['token']) ? $_GET['token'] : '';
if (!$is_cli && $token_recibido !== $token_esperado) {
    http_response_code(403);
    die('Acceso denegado. Se requiere ejecución mediante CLI o token válido.');
}
// Envolver todo en un bloque try/catch para manejo silencioso de errores (Cron Jobs)
try {
    // 2. Conexión a la Base de Datos
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // 3. Petición Real a la API (API-Football)
    $api_key = API_KEY;
    $league_id = LEAGUE_ID;
    $season = 2026;
    $url = "https://v3.football.api-sports.io/fixtures?league={$league_id}&season={$season}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "x-apisports-key: " . $api_key
    ));
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);
    if ($curl_error) {
        throw new Exception("Error de cURL: " . $curl_error);
    }
    $datos_api = json_decode($response, true);
    if (!isset($datos_api['response'])) {
        throw new Exception("La API no devolvió una respuesta válida (" . ($response ?: 'Vacío') . ")");
    }
    // 4. Lógica de Parcheo / Actualización
    echo "Iniciando sincronización...\n";
    $actualizados = 0;

    $traducciones = [
        'Spain' => 'España',
        'Germany' => 'Alemania',
        'Brazil' => 'Brasil',
        'France' => 'Francia',
        'Italy' => 'Italia',
        'Argentina' => 'Argentina', // Algunos coinciden, pero mejor tenerlos
        'Netherlands' => 'Países Bajos',
        'Belgium' => 'Bélgica',
        'England' => 'Inglaterra',
        'Portugal' => 'Portugal',
        'Mexico' => 'México',
        'USA' => 'Estados Unidos',
    ];


    foreach ($datos_api['response'] as $item) {

        $equipo_local_nombre = $item['teams']['home']['name'];
        $equipo_visitante_nombre = $item['teams']['away']['name'];

        // --- 2. APLICAR TRADUCCIÓN (Añadir justo aquí) ---
        if (isset($traducciones[$equipo_local_nombre])) {
            $equipo_local_nombre = $traducciones[$equipo_local_nombre];
        }
        if (isset($traducciones[$equipo_visitante_nombre])) {
            $equipo_visitante_nombre = $traducciones[$equipo_visitante_nombre];
        }
        
        // Validación de estructura mínima del item de la API
        if (
            !isset($item['teams']['home'], $item['teams']['away']) || 
            !isset($item['fixture']['status'], $item['fixture']['date']) ||
            !isset($item['goals'])
        ) {
            continue; // Saltar si faltan datos críticos
        }
        $equipo_local_nombre = $item['teams']['home']['name'];
        $equipo_visitante_nombre = $item['teams']['away']['name'];
        
        $goles_local = isset($item['goals']['home']) ? (int)$item['goals']['home'] : 0;
        $goles_visitante = isset($item['goals']['away']) ? (int)$item['goals']['away'] : 0;
        
        // Status: FT = Finalizado, NS = Pendiente
        $status_corto = $item['fixture']['status']['short'];
        $estado_interno = ($status_corto === 'FT' || $status_corto === 'PEN') ? 'finalizado' : 'pendiente';
        
        // Convertir fecha ISO a formato MySQL
        $fecha_iso = $item['fixture']['date'];
        $fecha_mysql = date('Y-m-d H:i:s', strtotime($fecha_iso));
        // A. Buscar los IDs de los equipos por nombre
        $stmtTeams = $pdo->prepare("SELECT id FROM equipos WHERE nombre = :nombre LIMIT 1");
        
        $stmtTeams->execute(['nombre' => $equipo_local_nombre]);
        $local = $stmtTeams->fetch(PDO::FETCH_ASSOC);
        
        $stmtTeams->execute(['nombre' => $equipo_visitante_nombre]);
        $visitante = $stmtTeams->fetch(PDO::FETCH_ASSOC);
        if ($local && $visitante) {
            $id_local = $local['id'];
            $id_visitante = $visitante['id'];
            // B. Buscar si el partido existe (emparejamiento)
            $stmtPartido = $pdo->prepare("SELECT id FROM partidos 
                                          WHERE (equipo_local_id = :idl AND equipo_visitante_id = :idv)
                                             OR (equipo_local_id = :idv AND equipo_visitante_id = :idl)
                                          LIMIT 1");
            $stmtPartido->execute(['idl' => $id_local, 'idv' => $id_visitante]);
            $partido_existente = $stmtPartido->fetch(PDO::FETCH_ASSOC);
            if ($partido_existente) {
                // Actualizar marcador y estado
                $stmtUpdate = $pdo->prepare("UPDATE partidos 
                                             SET goles_local = :gl, goles_visitante = :gv, 
                                                 estado = :est, fecha_hora = :fh 
                                             WHERE id = :id");
                $stmtUpdate->execute([
                    'gl' => $goles_local,
                    'gv' => $goles_visitante,
                    'est' => $estado_interno,
                    'fh' => $fecha_mysql,
                    'id' => $partido_existente['id']
                ]);
                $actualizados++;
            }
        }
    }
    echo "Sincronización finalizada satisfactoriamente.\n";
    echo "Total partidos procesados y actualizados: $actualizados\n";
} catch (Throwable $e) {
    // Si algo falla, lo registramos en error.log silenciosamente
    $timestamp = date('[Y-m-d H:i:s] ');
    error_log($timestamp . "ERROR en sync.php: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/error.log');
    
    if (!$is_cli) {
        http_response_code(500);
    }
    // No mostramos el error en pantalla si es un Cron Job
    exit(1);
}
?>