<?php

    namespace Administro\Route;

    use \Administro\Administro;

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

    class FormRoute extends Route {

        public function isValid($params) {
            return (isset($GLOBALS["AdministroPost"]) && count($params) == 2 && $params[1] == "form");
        }

        public function routeUser($params) {
            Administro::Instance()->formprocessor->processForm($params[2], $GLOBALS["AdministroPost"]);
            return false;
        }

    }

    class AdminRoute extends Route {

        public function isValid($params) {
            return (count($params) == 1 && $params[1] == "admin");
        }

        public function routeUser($params) {
            return "admin";
        }

    }

    class LoginRoute extends Route {

        public function isValid($params) {
            return (count($params) == 1 && $params[1] == "login");
        }

        public function routeUser($params) {
            return "login";
        }

    }

    class LogoutRoute extends Route {

        public function isValid($params) {
            return (count($params) == 1 && $params[1] == "logout");
        }

        public function routeUser($params) {
            Administro::Instance()->usermanager->logout();
            $this->redirect("", "good/Successfully logged out!");
            return false;
        }

    }
