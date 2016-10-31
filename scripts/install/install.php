<?php

    $options = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
    $context = stream_context_create($options);

    class Installer {

        public static function install() {
            global $context;
            $twig = self::loadFile("twig");
            // Check if the version installed is good
            if(isset($twig["required"], $twig["installed"]) && $twig["required"] == $twig["installed"]) {
                // The correct version is installed
                return;
            }
            // Load twig tags
            $tags = json_decode(file_get_contents("https://api.github.com/repos/twigphp/Twig/tags", false, $context), true);
            // Load the required tag
            $zipball = "";
            $version = "";
            if(isset($twig["required"])) {
                // Search for requested tag
                foreach ($tags as $data) {
                    if($data["name"] === "v".$twig["required"]) {
                        // Download this version
                        $zipball = $data["zipball_url"];
                        $version = $data["name"];
                    }
                }
            }
            // Check if a tag was found
            if(empty($zipball)) {
                die("Could not locate correct Twig version! Please report this issue to the <a href='https://github.com/FlibioWeb/Administro'>Administro GitHub</a>");
            }
            // Download and install Twig
            file_put_contents(BASEDIR."vendor/twig/twiginstall.zip", file_get_contents($zipball, false, $context));
            // Extract the zip file
            $zip = new ZipArchive;
            $res = $zip->open(BASEDIR."vendor/twig/twiginstall.zip");
            if ($res === TRUE) {
                $zip->extractTo(BASEDIR."vendor/twig");
                $zip->close();
                // Delete the zip file
                unlink(BASEDIR."vendor/twig/twiginstall.zip");
                // Move the files
                $destination = BASEDIR."vendor/twig/";
                $from = glob(BASEDIR."vendor/twig/twigphp-Twig-*/")[0];

                self::moveFolder($destination, $from);
            }
            // Save the new file
            $twig["installed"] = str_replace("v", "", $version);
            self::saveFile("twig", $twig);
            // Redirect
            header("Location: ".BASEPATH);
            die("Redirecting...");
        }

        // Moves a folder to a new location
        private static function moveFolder($destination, $from) {
            $toMove = scandir($from);
            // Loop through all files
            foreach ($toMove as $file) {
                // Make sure the file is not a navigation link
                if($file != "." && $file != "..") {
                    // Check if it is a directory
                    if(is_dir($destination.$file)) {
                        // Move the directory
                        $this->moveFolder($destination.$file."/", $from.$file."/");
                    } else {
                        // Move the file
                        rename($from.$file, $destination.$file);
                    }
                }
            }
            // Delete the initial folder
            rmdir($from);
        }

        private static function loadFile($dependency) {
            // Create vendor folder
            if(!is_dir(BASEDIR."vendor")) {
                mkdir(BASEDIR."vendor");
            }
            // Create dependency folder
            if(!is_dir(BASEDIR."vendor/$dependency")) {
                mkdir(BASEDIR."vendor/$dependency");
            }
            // Check if file exists
            if(file_exists(BASEDIR."vendor/$dependency/$dependency.json")) {
                return json_decode(file_get_contents(BASEDIR."vendor/$dependency/$dependency.json"), true);
            } else {
                self::saveFile($dependency, array());
            }
            return array();
        }

        private static function saveFile($dependency, $data) {
            // Create vendor folder
            if(!is_dir(BASEDIR."vendor")) {
                mkdir(BASEDIR."vendor");
            }
            // Create dependency folder
            if(!is_dir(BASEDIR."vendor/$dependency")) {
                mkdir(BASEDIR."vendor/$dependency");
            }
            // Save file
            file_put_contents(BASEDIR."vendor/$dependency/$dependency.json", json_encode($data));
        }

    }
