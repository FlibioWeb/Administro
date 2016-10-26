<?php

    // Initialize the session if necessary
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Define base variables
    define('BASEDIR', __DIR__."/");
    define('BASEPATH', implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/');

    // Load router
    require_once BASEDIR."scripts/router.php";

    $page = (new Router)->routeToPage();

    // Load the requested page
    require_once BASEDIR."partials/$page.php";
