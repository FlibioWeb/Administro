<?php

    namespace Administro\User;

    use \Administro\Lib\Spyc;

    // Start the session if needed
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    class UserManager {

        // Generate the user file on construction
        public function __construct() {
            $this->generateFile();
        }

        // Checks if the user is logged in
        public function isLoggedIn() {
            return isset($_SESSION["user"]);
        }

        public function getUser() {
            if($this->isLoggedIn()) {
                return $_SESSION["user"];
            } else {
                return array();
            }
        }

        // Checks if the user has the specified permission
        public function hasPermission($permission) {
            // Make sure the user is logged in
            if($this->isLoggedIn()) {
                // Get the permissions
                $permissions = $this->getUser()["permissions"];
                // Check the permission type
                $split = explode(".", $permission);
                if(count($split) == 1) {
                    // Simple permission
                    return in_array($permission, $permissions);
                } else if(count($split) == 2) {
                    // Advanced permission
                    $perm1 = $split[0];
                    return (in_array($permission, $permissions) || in_array($perm1.".super", $permissions));
                }
            }
            return false;
        }

        // Creates a new user
        public function createNewUser($user, $password, $display, $permissions) {
            // Generate the users file if it doesn't exist
            $this->generateFile();

            // Load the users file
            $data = Spyc::YAMLLoad(BASEDIR."config/users.yaml");

            // Make sure the user doesn't exist
            if(!isset($data[$user])) {
                // Hash the password
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]);
                // Set the user data
                $data[$user] = array("display" => $display, "password" => $hash, "permissions" => $permissions);
                // Save the file
                $this->saveFile($data);

                return true;
            }

            return false;
        }

        // Attempts to log the user in
        public function login($user, $password) {
            // Make sure the user is not logged in
            if(!$this->isLoggedIn()) {
                // Generate the users file if it doesn't exist
                $this->generateFile();

                // Load the users file
                $data = Spyc::YAMLLoad(BASEDIR."config/users.yaml");

                // Check if the username is in the file
                if(isset($data[$user])) {
                    $savedPassword = $data[$user]["password"];
                    $display = $data[$user]["display"];
                    $permissions = $data[$user]["permissions"];
                    // Verify the passwords
                    if(password_verify($password, $savedPassword)) {
                        // Set the session variables
                        $_SESSION["user"] = array("name" => $user, "display" => $display, "permissions" => $permissions);
                        return true;
                    }
                }
            }
            return false;
        }

        // Checks if any user exists
        public function anyUserExists() {
            $this->generateFile();

            // Load the users file
            $data = Spyc::YAMLLoad(BASEDIR."config/users.yaml");

            return (count($data) > 0);
        }

        // Generates the user file
        public function generateFile() {
            // Make the directory if it doesn't exist
            if(!file_exists(BASEDIR."config")) {
                mkdir(BASEDIR."config");
            }
            // Make the file if it doesn't exist
            if(!file_exists(BASEDIR."config/users.yaml")) {
                file_put_contents(BASEDIR."config/users.yaml", "");
            }
        }

        // Save the user file
        private function saveFile($data) {
            // Make sure the file exists
            $this->generateFile();
            // Write to the file
            file_put_contents(BASEDIR."config/users.yaml", Spyc::YAMLDump($data, false, false, true));
        }

        public function logout() {
            unset($_SESSION["user"]);
        }
    }
