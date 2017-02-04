<?php

    namespace Administro\Form;

    abstract class Form {

        function redirect($location, $message = "") {
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

        // Gets the id of the form
        abstract function getId();

        // Processes the form
        abstract function process($post);
    }
