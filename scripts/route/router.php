<?php

    namespace Administro\Route;

    use Administro\Route\Routes;

    class Router {

        private $routes;

        public function __construct() {
            // Add all the routes
            $this->routes = array(
                new PageRoute,
                );
        }

        public function routeToPage() {
            $uri = substr(strtolower($_SERVER['REQUEST_URI']), strlen(BASEPATH));
            if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
            $request = '/' . trim($uri, '/');
            $params = array_filter(explode("/", $request));

            // If the router finds nothing load the default page
            $page = "page";

            foreach ($this->routes as $route) {
                // Check if the request matches the route
                if($route->isValid($params)) {
                    // Route the user
                    $page = $route->routeUser($params);
                } else {
                    continue;
                }
            }

            return $page;
        }

    }
