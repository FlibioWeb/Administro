<?php

    namespace Administro\Admin;

    use \Administro\Administro;

    class AdminRoutes {

        var $routes;

        public function __construct() {
            $this->routes = array(new HomeRoute, new PagesRoute, new PageRoute);
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

    class PageRoute extends AdminRoute {

        function isVisible() {
            return false;
        }

        function getIcon() {
            return false;
        }

        function getName() {
            return "Page";
        }

        function getPartial() {
            return "page";
        }

        function isValid($params) {
            return (count($params) == 2 && $params[0] == "pages" && Administro::Instance()->pagemanager->pageExists($params[1]));
        }

    }
