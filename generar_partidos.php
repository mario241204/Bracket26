<?php
// Configuración de tu base de datos XAMPP
$host = 'localhost';
$db   = 'bracket26';
$user = 'root';
$pass = ''; // Contraseña por defecto en XAMPP suele estar vacía

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Limpiamos los partidos anteriores
    $pdo->exec("TRUNCATE TABLE partidos");

    // 2. Obtenemos todos los equipos ordenados por grupo
    $stmt = $pdo->query("SELECT * FROM equipos ORDER BY grupo, id");
    $equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupamos los equipos por su letra (A, B, C...)
    $grupos = [];
    foreach ($equipos as $equipo) {
        $grupos[$equipo['grupo']][] = $equipo;
    }

    // 3. Generamos los 3 partidos para cada equipo
    $fecha_inicio = strtotime('2026-06-11 18:00:00');
    $grupo_index = 0; // Para el desfase de inicio de cada grupo

    foreach ($grupos as $letra => $integrantes) {
        if (count($integrantes) == 4) {
            $e1 = $integrantes[0]['id'];
            $e2 = $integrantes[1]['id'];
            $e3 = $integrantes[2]['id'];
            $e4 = $integrantes[3]['id'];

            // Desfase base del grupo (A=0, B=1, C=2...)
            $base_grupo = $grupo_index; 
            
            // Jornada 1: Fecha base del grupo
            $j1_dias = $base_grupo;
            $fecha1 = date('Y-m-d H:i:s', strtotime("+$j1_dias days", $fecha_inicio));
            $pdo->exec("INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha_hora, fase) VALUES ($e1, $e2, '$fecha1', 'grupo')");
            $pdo->exec("INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha_hora, fase) VALUES ($e3, $e4, '$fecha1', 'grupo')");

            // Jornada 2: +4 días desde la Jornada 1
            $j2_dias = $j1_dias + 4;
            $fecha2 = date('Y-m-d H:i:s', strtotime("+$j2_dias days", $fecha_inicio));
            $pdo->exec("INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha_hora, fase) VALUES ($e1, $e3, '$fecha2', 'grupo')");
            $pdo->exec("INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha_hora, fase) VALUES ($e2, $e4, '$fecha2', 'grupo')");

            // Jornada 3: +4 días desde la Jornada 2
            $j3_dias = $j2_dias + 4;
            $fecha3 = date('Y-m-d H:i:s', strtotime("+$j3_dias days", $fecha_inicio));
            $pdo->exec("INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha_hora, fase) VALUES ($e1, $e4, '$fecha3', 'grupo')");
            $pdo->exec("INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha_hora, fase) VALUES ($e2, $e3, '$fecha3', 'grupo')");
            
            $grupo_index++;
        }
    }
    echo "¡Los 72 partidos de la fase de grupos se han generado con éxito!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>