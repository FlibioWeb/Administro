<?php

    namespace Administro\Form;

    // Start the session if needed
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Various utilities for form creation
    class FormUtils {

        // Generates a unique token
        public static function generateToken($formName) {
            $token = hash('ripemd160', md5(microtime()));

            $_SESSION["token-".$formName] = $token;

            return $token;
        }

        // Verifies the token with the form
        public static function verifyToken($formName, $token) {
            // Check if the token exists in session
            if(isset($_SESSION["token-".$formName])) {
                // Check if the tokens match
                if($_SESSION["token-".$formName] == $token) {
                    unset($_SESSION["token-".$formName]);
                    return true;
                }
                unset($_SESSION["token-".$formName]);
            }
            return false;
        }

        // Verifies the token with the form, without deleting it
        public static function verifyTokenSave($formName, $token) {
            // Check if the token exists in session
            if(isset($_SESSION["token-".$formName])) {
                // Check if the tokens match
                if($_SESSION["token-".$formName] == $token) {
                    return true;
                }
            }
            return false;
        }

        // Checks if parameters are present and returns them
        public static function getParameters($parameters, $post) {
            $verifiedParamaters = array();

            // Verify that all of the parameters exist
            $fail = false;
            foreach ($parameters as $param) {
                if(isset($post[$param])) {
                    $verifiedParamaters[$param] = htmlentities($post[$param]);
                } else {
                    $fail = true;
                    break;
                }
            }
            // Return the parameters if it was successful
            if($fail) {
                return false;
            } else {
                return $verifiedParamaters;
            }
        }

        // Gets all parameters and verifies the token
        public static function getParametersWithToken($parameters, $post, $formName, $delToken = true) {
            if($delToken) {
                // Verify the token
                if(!isset($post["token"]) || !self::verifyToken($formName, $post["token"])) {
                    return false;
                }
            } else {
                // Verify the token
                if(!isset($post["token"]) || !self::verifyTokenSave($formName, $post["token"])) {
                    return false;
                }
            }

            $verifiedParamaters = array();

            // Load the parameters
            $fail = false;
            foreach ($parameters as $param) {
                if(isset($post[$param])) {
                    $verifiedParamaters[$param] = htmlentities($post[$param]);
                } else {
                    $fail = true;
                    break;
                }
            }
            if($fail) {
                return false;
            } else {
                return $verifiedParamaters;
            }
        }

        // Verifies the token with a form
        public static function verifyPostToken($post, $formName) {
            if(!isset($post["token"]) || !self::verifyToken($formName, $post["token"])) {
                return false;
            }

            return true;
        }
    }
