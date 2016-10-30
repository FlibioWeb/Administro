<?php

    namespace Administro\Route;

    use \Administro\Administro;

    class RouteManager {

        private $routes;

        public function __construct() {
            $this->routes = array(new FormRoute, new PageRoute, new AdminRoute, new LoginRoute, new LogoutRoute);
        }

        // Registers a new route.
        public function registerRoute($route) {
            if($route instanceof Route) {
                array_push($this->routes, $route);
            }
            return false;
        }

        // Gets all routes.
        public function getRoutes() {
            return $this->routes;
        }

        // Routes the user.
        public function routeUser() {
            // Get the parameters
            $uri = substr(strtolower($_SERVER['REQUEST_URI']), strlen(BASEPATH));
            if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
            $request = '/' . trim($uri, '/');
            $params = array_filter(explode("/", $request));

            // If the router finds nothing load the default partial
            $partial = "page";

            foreach ($this->routes as $route) {
                // Check if the request matches the route
                if($route->isValid($params)) {
                    // Route the user
                    $partial = $route->routeUser($params);
                } else {
                    continue;
                }
            }

            $partialmanager = Administro::Instance()->partialmanager;

            if($partialmanager->partialExists($partial)) {
                // Load the requested partial
                require_once $partialmanager->getPartial($partial);
            } else {
                // Load the default partial
                require_once $partialmanager->getPartial("page");
            }
        }

    }
