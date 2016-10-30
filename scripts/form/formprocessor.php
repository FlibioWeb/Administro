<?php

    namespace Administro\Form;

    use \Administro\Administro;

    class FormProcessor {

        // Processes all forms
        public function processForm($formname, $post) {
            switch ($formname) {

                case 'login':
                    $params = FormUtils::getParametersWithToken(array("username", "password"), $_POST, "login");

                    if($params != false) {
                        $username = $params["username"];
                        $password = $params["password"];

                        // Attempt to login
                        if(Administro::Instance()->usermanager->login($username, $password)) {
                            // Success
                            $this->redirect("", "good/Logged in!");
                        } else {
                            // Login failed
                            $this->redirect("login", "bad/Invalid login!");
                        }
                    } else {
                        // Invalid parameters
                        $this->redirect("login", "bad/Invalid parameters!");
                    }
                    break;

                default:
                    // Redirect to default page
                    $this->redirect("");
                    break;
            }
        }

        // Redirects the user
        private function redirect($location, $message = "") {
            // Parse the message
            if(!empty($message)) {
                $max = 1;
                if(strlen($message) >= 3 && substr($message, 0, 3) == "bad") {
                    if(isset($_SESSION["message-bad"])) {
                        $_SESSION["message-bad"].=" - ";
                    }
                    $_SESSION["message-bad"].= str_ireplace("bad/", "", $message, $max);
                }
                if(strlen($message) >= 4 && substr($message, 0, 4) == "good") {
                    if(isset($_SESSION["message-good"])) {
                        $_SESSION["message-good"].=" - ";
                    }
                    $_SESSION["message-good"].= str_ireplace("good/", "", $message, $max);
                }
            }
            // Redirect the user
            header("Location: ".BASEPATH.$location);
            die("Redirecting...");
        }

    }
