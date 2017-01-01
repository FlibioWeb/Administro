<?php

    namespace Administro\Form;

    use \Administro\Administro;
    use \Administro\Config\FileUtils;

    class FormProcessor {

        // Processes all forms
        public function processForm($formname, $post) {
            // Administro variables
            $administro = Administro::Instance();
            $usermanager = $administro->usermanager;
            $pagemanager = $administro->pagemanager;
            $updater = $administro->updater;
            // Switch on the form name
            switch ($formname) {

                case 'parsemarkdown':
                    $params = FormUtils::getParametersWithToken(array("page", "content"), $post, "parsemarkdown", false);

                    if($params != false) {
                        $page = $params["page"];

                        echo $pagemanager->renderPage($page, $params["content"]);
                        die();
                    } else {
                        // Invalid parameters
                        die("Error rendering page!");
                    }
                    break;

                case 'savepage':
                    // Verify permission
                    if(!$usermanager->hasPermission("admin.savepage")) {
                        // User can not do this
                        redirect("", "bad/You do not have permission to do that!");
                    }
                    // Get parameters
                    $params = FormUtils::getParametersWithToken(array("page", "content"), $post, "savepage", false);

                    if($params != false) {
                        $page = $params["page"];

                        die($pagemanager->savePageContent($page, $params["content"]));
                    } else {
                        // Invalid parameters
                        die(false);
                    }
                    break;

                case 'uploadfile':
                    // Verify permission
                    if(!$usermanager->hasPermission("admin.uploadfile")) {
                        // User can not do this
                        $this->redirect("admin", "bad/Invalid parameters!");
                    }
                    // Verify parameters
                    if(!isset($post["page"], $post["token"], $post["submit"])) {
                        // Invalid parameters
                        $this->redirect("admin", "bad/Invalid parameters!");
                    }
                    $page = $post["page"];
                    // Verify token
                    if(!FormUtils::verifyToken("uploadfile", $post["token"])) {
                        // Invalid token
                        $this->redirect("admin/pages/$page", "bad/Invalid token!");
                    }
                    $target_dir = BASEDIR."pages/$page/files/";
                    $target_file = $target_dir . basename($_FILES["toUpload"]["name"]);
                    // Check if file already exists
                    if (file_exists($target_file)) {
                        $this->redirect("admin/pages/$page", "bad/File already exists!");
                    }
                    // Check file size
                    if ($_FILES["fileToUpload"]["size"] > 5000000) {
                        $this->redirect("admin/pages/$page", "bad/File must be under 5MB!");
                    }
                    // Finish upload
                    if (move_uploaded_file($_FILES["toUpload"]["tmp_name"], $target_file)) {
                        $this->redirect("admin/pages/$page", "good/File uploaded!");
                    } else {
                        $this->redirect("admin/pages/$page", "bad/File upload error!");
                    }
                    break;

                case 'login':
                    $params = FormUtils::getParametersWithToken(array("username", "password"), $post, "login");

                    if($params != false) {
                        $username = $params["username"];
                        $password = $params["password"];

                        // Attempt to login
                        if($usermanager->login($username, $password)) {
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

                case 'update':
                    // Verify permission
                    if(!$usermanager->hasPermission("admin.update")) {
                        // User can not do this
                        redirect("", "bad/You do not have permission to do that!");
                    }
                    $params = FormUtils::verifyPostToken($post, "update");

                    if($params !== false) {
                        // Check if an update is available
                        if($updater->checkForUpdate()) {
                            // Download the update
                            if($updater->downloadUpdate()) {
                                // Success
                                $this->redirect("admin", "good/Installed update!");
                            } else {
                                // Update failed
                                $this->redirect("admin", "bad/Failed to install update!");
                            }
                        } else {
                            // No update available
                            $this->redirect("admin", "bad/No update available!");
                        }
                    } else {
                        // Invalid parameters
                        $this->redirect("admin", "bad/Invalid parameters!");
                    }
                    break;

                case 'clearcache':
                    // Verify permission
                    if(!$usermanager->hasPermission("admin.clearcache")) {
                        // User can not do this
                        redirect("", "bad/You do not have permission to do that!");
                    }
                    $params = FormUtils::verifyPostToken($post, "clearcache");

                    if($params !== false) {
                        // Clear Twig cache
                        @FileUtils::deleteFolder(BASEDIR."cache");
                        // Clear update cache
                        $updater->clearCache();
                        // Success
                        $this->redirect("admin", "good/Cleared cache!");
                    } else {
                        // Invalid parameters
                        $this->redirect("admin", "bad/Invalid parameters!");
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
