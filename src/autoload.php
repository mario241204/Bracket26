<?php

spl_autoload_register(function ($class_name) {
    // Definir los directorios base donde buscar clases
    $directories = [
        __DIR__ . '/config/',
        __DIR__ . '/controllers/',
        __DIR__ . '/models/'
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
