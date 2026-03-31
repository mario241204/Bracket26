<?php

/**
 * Front Controller para peticiones API
 * Todo el tráfico hacia /api.php pasa por aquí y se despacha al ApiController.
 */

// Cargar configuración global
require_once __DIR__ . '/src/config/config.php';

// Cargar el autoloader de clases
require_once __DIR__ . '/src/autoload.php';

// Instanciar el controlador y despachar la petición
$controller = new ApiController();
$controller->handleRequest();
