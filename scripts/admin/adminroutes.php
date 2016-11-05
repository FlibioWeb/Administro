<?php

    namespace Administro\Admin;

    class AdminRoutes {

        var $routes;

        public function __construct() {
            $this->routes = array(new HomeRoute, new PagesRoute);
        }

        // Adds an admin route.
        public function addAdminRoute($route) {
            array_push($this->routes, $route);
        }

        // Gets all admin routes.
        public function getAdminRoutes() {
            return $this->routes;
        }
    }

    /* All default admin routes */

    class HomeRoute extends AdminRoute {

        function isVisible() {
            return true;
        }

        function getIcon() {
            return "home";
        }

        function getName() {
            return "Home";
        }

        function getPartial() {
            return "home";
        }

        function isValid($params) {
            return (count($params) == 1 && $params[0] == "home");
        }

    }

    class PagesRoute extends AdminRoute {

        function isVisible() {
            return true;
        }

        function getIcon() {
            return "file-text";
        }

        function getName() {
            return "Pages";
        }

        function getPartial() {
            return "pages";
        }

        function isValid($params) {
            return (count($params) == 1 && $params[0] == "pages");
        }

    }
