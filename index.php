<?php

    // Initialize the session if necessary
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Verify the PHP version
    if (version_compare(phpversion(), '5.5') < 0) {
        die("<b>Notice: </b>Administro requires PHP version 5.5 or greater! You are running version ".phpversion()."!");
    }

    // Define base variables
    define('BASEDIR', __DIR__."/");
    define('BASEPATH', implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/');

    // Include all scripts
    foreach (glob("{scripts/**/*.php,scripts/*.php}", GLOB_BRACE) as $filename) {
        require_once $filename;
    }

    // Include all plugins
    foreach (glob("plugins/**/*.php") as $filename) {
        require_once $filename;
    }

    // Load the router
    use \Administro\Route\Router;

    $page = (new Router)->routeToPage();

    // Load the requested page
    require_once BASEDIR."partials/$page.php";
