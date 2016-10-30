<?php

    namespace Administro\Admin;

    class AdminRoutes {

        var $routes;

        public function __construct() {
            $this->routes = array();
            // Home Route
            $this->routes["home"]["icon"] = "home";
            $this->routes["home"]["partial"] = "home";
            $this->routes["home"]["display"] = "Home";
            // Pages Route
            $this->routes["pages"]["icon"] = "file-text";
            $this->routes["pages"]["partial"] = "pages";
            $this->routes["pages"]["display"] = "Pages";
        }

        // Adds an admin route, using an FA icon, display name, and an admin partial.
        public function addAdminRoute($name, $icon, $display, $partial) {
            $this->routes[$name]["icon"] = $icon;
            $this->routes[$name]["partial"] = $partial;
            $this->routes[$name]["display"] = $display;
        }

        // Gets one admin route.
        public function getAdminRoute($name) {
            return $this->routes[$name];
        }

        // Gets all admin routes.
        public function getAdminRoutes() {
            return $this->routes;
        }
    }
