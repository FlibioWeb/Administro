<?php

    // Verify the PHP version
    if(PHP_VERSION_ID < 50500) {
        die("Administro requires PHP v5.5.0 or greater!");
    }

    // Start the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Load Composer dependencies
    require_once(__DIR__ . '/vendor/autoload.php');

    // Load the website
    $administro = new Administro(__DIR__);

    // Run the website
    $administro->run();
