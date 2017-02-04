<?php

    use \Administro\Administro;

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
    define('DATADIR', BASEDIR."/data/");
    define('BASEPATH', implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/');

    // Accept post data if it is set
    if(isset($_POST)) {
        $GLOBALS["AdministroPost"] = $_POST;
    }

    // Include all scripts
    foreach (glob("{scripts/**/*.php,scripts/*.php}", GLOB_BRACE) as $filename) {
        require_once $filename;
    }

    // Install if needed
    Installer::install();

    // Include all plugins
    foreach (glob("plugins/**/*.php") as $filename) {
        require_once $filename;
    }

    // Load the router
    Administro::Instance()->routemanager->routeUser();
