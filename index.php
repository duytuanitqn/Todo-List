<?php
    
    define('DS', DIRECTORY_SEPARATOR, true);
    define('BASE_PATH', __DIR__ . DS, TRUE);
    
    /**
     * Load vendor
     */
    require BASE_PATH.'vendor/autoload.php';
    
    /**
     * Load file env
     */
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
    
    /**
     * Load Router
     */
    require BASE_PATH.'core/Router/Router.php';
    require BASE_PATH.'routes/web.php';