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

    class FileRoute extends Route {

        public function isValid($params) {
            return (count($params) > 1 && $params[1] == "file");
        }

        public function routeUser($params) {
            $file = "";
            for($i = 2; $i <= count($params); $i++) {
                if(empty($file)) {
                    $file = $params[$i];
                } else {
                    $file.="/".$params[$i];
                }
            }
            die(Administro::Instance()->filemanager->processFile($file));
        }

    }

    class FormRoute extends Route {

        public function isValid($params) {
            return (isset($GLOBALS["AdministroPost"]) && count($params) == 2 && $params[1] == "form");
        }

        public function routeUser($params) {
            die(Administro::Instance()->formmanager->processForm($params[2], $GLOBALS["AdministroPost"]));
        }

    }

    class AdminRoute extends Route {

        public function isValid($params) {
            if(count($params) > 0 && $params[1] == "admin") {
                // Get admin routes
                $routes = Administro::Instance()->adminroutes->getAdminRoutes();
                // Check if this is the home route
                if(count($params) == 1) {
                    return true;
                } else {
                    // Try the other routes
                    foreach ($routes as $route) {
                        $newParams = $params;
                        array_splice($newParams, 0, 1);
                        // Check if the route is good.
                        if($route->isValid($newParams)) {
                            // Set the route.
                            $GLOBALS["AdministroAdminRoute"] = $route;
                            // Get new parameters
                            $pageParams = $newParams;
                            array_splice($pageParams, 0, 1);
                            $GLOBALS["AdministroAdminParams"] = $pageParams;
                            return true;
                        }
                    }
                    return false;
                }
            }
            return false;
        }

        public function routeUser($params) {
            // Redirect to admin home if needed
            if(count($params) == 1) {
                $this->redirect("admin/home");
            } else {
                $route = $GLOBALS["AdministroAdminRoute"];
                // Load the correct admin page
                $GLOBALS["AdministroAdminPage"] = $route->getPartial();
            }
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
