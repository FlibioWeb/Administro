<?php

    namespace Administro\Route;

    use \Administro\Administro;

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
            if(count($params) == 1 && Administro::Instance()->pagemanager->pageExists($params[1])) {
                return true;
            }
            return false;
        }

        public function routeUser($params) {
            // Get ConfigManager
            $configManager = Administro::Instance()->configmanager;
            // Load the page partial by default
            if(count($params) == 0) {
                $GLOBALS["requestedPage"] = $configManager->getConfiguration()["default-page"];
            }
            if(count($params) == 1) {
                // Check if the page is the default page
                if($configManager->getConfiguration()["default-page"] == $params[1]) {
                    // Redirect the user to the base path
                    $this->redirect("");
                }
                $GLOBALS["requestedPage"] = $params[1];
            }
            return "page";
        }

    }