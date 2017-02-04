<?php

    namespace Administro\Form;

    use \Administro\Administro;
    use \Administro\Config\FileUtils;

    class ParseForm extends Form {

        public function getId() {
            return "parsemarkdown";
        }

        public function process($post) {
            $params = FormUtils::getParametersWithToken(array("page", "content"), $post, "parsemarkdown", false);

            if($params != false) {
                $page = $params["page"];

                return Administro::Instance()->pagemanager->renderPage($page, $params["content"]);
            } else {
                // Invalid parameters
                return "Error rendering page!";
            }
        }

    }

    class SavePageForm extends Form {

        public function getId() {
            return "savepage";
        }

        public function process($post) {
            // Verify permission
            if(!Administro::Instance()->usermanager->hasPermission("admin.savepage")) {
                // User can not do this
                $this->redirect("", "bad/You do not have permission to do that!");
            }
            // Get parameters
            $params = FormUtils::getParametersWithToken(array("page", "content"), $post, "savepage", false);

            if($params != false) {
                $page = $params["page"];

                return Administro::Instance()->pagemanager->savePageContent($page, $params["content"]);
            } else {
                // Invalid parameters
                return "Invalid parameters!";
            }
        }

    }

    class UploadForm extends Form {

        public function getId() {
            return "uploadfile";
        }

        public function process($post) {
            // Verify permission
            if(!Administro::Instance()->usermanager->hasPermission("admin.uploadfile")) {
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
        }

    }

    class LoginForm extends Form {

        public function getId() {
            return "login";
        }

        public function process($post) {
            $params = FormUtils::getParametersWithToken(array("username", "password"), $post, "login");

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
        }

    }

    class UpdateForm extends Form {

        public function getId() {
            return "update";
        }

        public function process($post) {
            // Verify permission
            if(!Administro::Instance()->usermanager->hasPermission("admin.update")) {
                // User can not do this
                redirect("", "bad/You do not have permission to do that!");
            }
            $params = FormUtils::verifyPostToken($post, "update");
            $updater = Administro::Instance()->updater;
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
        }

    }

    class CacheForm extends Form {

        public function getId() {
            return "clearcache";
        }

        public function process($post) {
            // Verify permission
            if(!Administro::Instance()->usermanager->hasPermission("admin.clearcache")) {
                // User can not do this
                redirect("", "bad/You do not have permission to do that!");
            }
            $params = FormUtils::verifyPostToken($post, "clearcache");
            $updater = Administro::Instance()->updater;
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
        }

    }
