<?php

    namespace Administro\Route;

    abstract class Route {

        function redirect($location) {
            // Redirect the user
            header("Location: ".BASEPATH.$location);
            die("Redirecting...");
        }

        // Checks if the route is valid for the given parameters
        abstract function isValid($params);

        // Routes the user and outputs a partial id.
        abstract function routeUser($params);
    }
