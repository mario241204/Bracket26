<?php
/**
 * =========================================================================
 * BRACKET26 - SINCRONIZADOR AUTOMÁTICO DE PARTIDOS (CRON JOB)
 * =========================================================================
 * 
 * Este script automatiza la extracción de resultados y actualizaciones 
 * de fechas desde una API externa (ej. API-Football) hacia la base de datos local.
 * 
 * PROTECCIÓN: 
 * Este archivo está protegido. Solo se puede ejecutar a través de la línea de
 * comandos (CLI) o proporcionando un token de seguridad vía HTTP GET.
 * 
 * =========================================================================
 * INSTRUCCIONES DE CONFIGURACIÓN DEL CRON JOB (LINUX / cPANEL):
 * =========================================================================
 * Para que este script se ejecute automáticamente cada 15 minutos, debes 
 * añadir la siguiente tarea cron en tu servidor:
 * 
 * 1. Accede a cPanel -> Tareas Cron (Cron Jobs) o edita el crontab vía SSH (`crontab -e`).
 * 2. Selecciona "Una vez cada 15 minutos" o usa la expresión: * /15 * * * * (quitando el espacio entre el asterisco y la barra)
 * 3. En el comando, pon la ruta absoluta de ejecución de PHP hacia este archivo:
 * 
 * Comando SSH/cPanel:
 * /usr/local/bin/php /home/tunombre/public_html/Bracket26/sync.php
 * 
 * Alternativa usando wget (HTTP con token):
 * wget -q -O /dev/null "https://tudominio.com/sync.php?token=SECRETO_CRON_2026"
 * 
 * =========================================================================
 */

// 1. Capa de Seguridad (Previene ejecución pública no autorizada)
require_once __DIR__ . '/src/config/config.php';

$token_esperado = CRON_TOKEN;
$is_cli = (php_sapi_name() === 'cli');
$token_recibido = isset($_GET['token']) ? $_GET['token'] : '';

if (!$is_cli && $token_recibido !== $token_esperado) {
    http_response_code(403);
    die('Acceso denegado. Se requiere ejecución mediante CLI o token válido.');
}

// 2. Conexión a la Base de Datos
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la BD en el cron: " . $e->getMessage());
}

// 3. Simulación de Petición a la API (API-Football)
// NOTA: En producción, descomentar curl_exec y usar tu KEY.
$api_key = API_KEY;
$league_id = 1; // ID ficticio del Mundial 2026
$season = 2026;

$url = "https://v3.football.api-sports.io/fixtures?league={$league_id}&season={$season}";

/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "x-apisports-key: " . $api_key
));
$response = curl_exec($ch);
curl_close($ch);
$datos_api = json_decode($response, true);
*/

// MOCK - Dejado como ejemplo vacío (Descomentar y adaptar para pruebas):
/*
$mock_json = '{
    "response": []
}';
$datos_api = json_decode($mock_json, true);
*/

// NOTA: Para producción, asegúrate de que el código cURL de arriba sea el que establece $datos_api.
if (!isset($datos_api['response'])) {
    // Si no ha cargado nada (mock comentado y curl comentado), detenemos la ejecución silenciosa.
    die();
}


// 4. Lógica de Parcheo / Actualización
echo "Iniciando sincronización...\n";
$actualizados = 0;

foreach ($datos_api['response'] as $item) {
    // Validación de estructura mínima del item de la API
    if (
        !isset($item['teams']['home'], $item['teams']['away']) || 
        !isset($item['fixture']['status'], $item['fixture']['date']) ||
        !isset($item['goals'])
    ) {
        echo "Aviso: Item de la API con estructura incompleta. Saltando...\n";
        continue;
    }

    $equipo_local_nombre = $item['teams']['home']['name'];
    $equipo_visitante_nombre = $item['teams']['away']['name'];
    
    // Validar que los goles sean numéricos o null (la API puede devolver null si no ha empezado)
    $goles_local = isset($item['goals']['home']) ? (int)$item['goals']['home'] : 0;
    $goles_visitante = isset($item['goals']['away']) ? (int)$item['goals']['away'] : 0;
    
    // Status en API-Football (FT = Full Time, NS = Not Started, etc.)
    $status_corto = $item['fixture']['status']['short'];
    $estado_interno = ($status_corto === 'FT' || $status_corto === 'PEN') ? 'finalizado' : 'pendiente';
    
    // Convertir ISO 8601 a MySQL DATETIME
    $fecha_iso = $item['fixture']['date'];
    $fecha_mysql = date('Y-m-d H:i:s', strtotime($fecha_iso));

    // A. Buscar los IDs de los equipos según su nombre 
    // (En producción se recomienda tener una columna api_team_id en la tabla equipos).
    $stmtTeams = $pdo->prepare("SELECT id FROM equipos WHERE nombre = :nombre LIMIT 1");
    
    $stmtTeams->execute(['nombre' => $equipo_local_nombre]);
    $local = $stmtTeams->fetch(PDO::FETCH_ASSOC);
    
    $stmtTeams->execute(['nombre' => $equipo_visitante_nombre]);
    $visitante = $stmtTeams->fetch(PDO::FETCH_ASSOC);

    if ($local && $visitante) {
        $id_local = $local['id'];
        $id_visitante = $visitante['id'];

        // B. Buscar si el partido existe (por equipos emparejados) 
        // ATENCIÓN: En caso de eliminatorias, el partido puede que exista pero tenga equipo_local_id o visitante en NULL
        $stmtPartido = $pdo->prepare("SELECT id, estado FROM partidos 
                                      WHERE (equipo_local_id = :idl AND equipo_visitante_id = :idv)
                                         OR (equipo_local_id = :idv AND equipo_visitante_id = :idl)
                                      LIMIT 1");
        $stmtPartido->execute(['idl' => $id_local, 'idv' => $id_visitante]);
        $partido_existente = $stmtPartido->fetch(PDO::FETCH_ASSOC);

        if ($partido_existente) {
            // Actualizar partido
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
            echo "Partido Actualizado: $equipo_local_nombre vs $equipo_visitante_nombre [$estado_interno]\n";
        } else {
            // Si no existe el emparejamiento, revisamos si toca rellenar un hueco de Eliminatorias
            // (por ej. un partido de Cuartos de final con equipo_local_id NULL).
            // Esto requeriría lógica avanzada para saber QUÉ partido de cuartos es.
            // Por simplicidad, aquí lo insertamos si no existe, o se podría mapear contra un api_fixture_id.
            // echo "Partido no enlazado - Ignorando o se insertaría en fase correspondiente.\n";
        }
    }
}

echo "Sincronización finalizada. Total partidos actualizados: $actualizados\n";
?>
