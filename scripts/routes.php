<?php

    require_once BASEDIR."scripts/pagemanager.php";
    require_once BASEDIR."scripts/configmanager.php";

    abstract class Route {

        function redirect($location) {
            // Redirect the user
            header("Location: ".BASEPATH.$location);
            die("Redirecting...");
        }

        abstract function isValid($params);

        abstract function routeUser($params);
    }

    class PageRoute extends Route {

        public function isValid($params) {
            if(count($params) == 0) {
                return true;
            }
            if(count($params) == 1 && PageManager::pageExists($params[1])) {
                return true;
            }
            return false;
        }

        public function routeUser($params) {
            // Load the page partial by default
            if(count($params) == 0) {
                $GLOBALS["requestedPage"] = ConfigManager::getConfiguration()["default-page"];
            }
            if(count($params) == 1) {
                // Check if the page is the default page
                if(ConfigManager::getConfiguration()["default-page"] == $params[1]) {
                    // Redirect the user to the base path
                    $this->redirect("");
                }
                $GLOBALS["requestedPage"] = $params[1];
            }
            return "page";
        }

    }
