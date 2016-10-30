<?php

    namespace Administro\Update;

    use \DateTime;
    use \ZipArchive;

    // Variables used for connecting to GitHub
    $options = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
    $context = stream_context_create($options);
    // Other settings
    $updateFile = BASEDIR."config/updater.json";

    class Updater {

        // Gets the current installed version
        public function getCurrentVersion() {
            global $updateFile;
            if($this->hasVersionData()) {
                // Load the data
                $data = json_decode(file_get_contents($updateFile), true);
                // Return the version name
                return $data["version"]["name"];
            } else {
                return false;
            }
        }

        // Checks if an upate is available
        public function checkForUpdate() {
            global $updateFile;
            if($this->hasVersionData()) {
                $data = json_decode(file_get_contents($updateFile), true);
                // Load the installed version information
                $installedDate = $data["version"]["date"];
                // Load the latest release information
                $latest = $this->getLatestRelease();
                $date = $latest["published_at"];
                // Check if the current release is newer than the installed
                return ((new DateTime($date)) > (new DateTime($installedDate)));
            } else {
                return true;
            }
        }

        // Downloads an update if it is available
        public function downloadUpdate() {
            global $updateFile;
            // Make sure there is an update available
            if($this->checkForUpdate()) {
                $data = json_decode(file_get_contents($updateFile), true);
                // Get the latest release
                $latest = $this->getLatestRelease();
                $data["version"]["date"] = $latest["published_at"];
                $data["version"]["name"] = $latest["tag_name"];
                $data["version"]["id"] = $latest["id"];
                // Install the update
                if($this->installUpdate($latest)) {
                    file_put_contents($updateFile, json_encode($data));
                    return true;
                }
            }
            return false;
        }

        // Installs an update
        private function installUpdate($latest) {
            global $context;
            // Get the latest administro version
            file_put_contents(BASEDIR."administroinstall.zip", file_get_contents($latest["zipball_url"], false, $context));
            // Extract the zip file
            $zip = new ZipArchive;
            $res = $zip->open(BASEDIR."administroinstall.zip");
            if ($res === TRUE) {
                $zip->extractTo(BASEDIR);
                $zip->close();
                // Delete the zip file
                unlink(BASEDIR."administroinstall.zip");
                // Move the files
                $destination = BASEDIR;
                $from = glob(BASEDIR."FlibioWeb-Administro-*/")[0];

                $this->moveFolder($destination, $from);

                return true;
            }
            return false;
        }

        // Moves a folder to a new location
        private function moveFolder($destination, $from) {
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

        // Gets the latest release
        private function getLatestRelease() {
            global $updateFile, $context;
            if($this->hasCacheData()) {
                $data = json_decode(file_get_contents($updateFile), true);
                $cache = $data["cache"];
                // Check if the cache needs to be reloaded
                $elapsed = (new DateTime(date("Y-m-d H:i:s")))->getTimestamp() - (new DateTime($cache["date"]))->getTimestamp();
                if($elapsed >= 1800) {
                    // Reload the cache
                    $latest = json_decode(file_get_contents("https://api.github.com/repos/FlibioWeb/Administro/releases/latest", false, $context), true);
                    $data["cache"]["content"]["id"] = $latest["id"];
                    $data["cache"]["content"]["tag_name"] = $latest["tag_name"];
                    $data["cache"]["content"]["published_at"] = $latest["published_at"];
                    $data["cache"]["content"]["zipball_url"] = $latest["zipball_url"];
                    $data["cache"]["date"] = (new DateTime)->format("Y-m-d H:i:s");
                    // Save the cache
                    file_put_contents($updateFile, json_encode($data));

                    return $data["cache"]["content"];
                } else {
                    // Return the release from the cache
                    return $cache["content"];
                }
            } else {
                $data = json_decode(file_get_contents($updateFile), true);
                // Load the latest data
                $latest = json_decode(file_get_contents("https://api.github.com/repos/FlibioWeb/Administro/releases/latest", false, $context), true);
                $data["cache"]["content"]["id"] = $latest["id"];
                $data["cache"]["content"]["tag_name"] = $latest["tag_name"];
                $data["cache"]["content"]["published_at"] = $latest["published_at"];
                $data["cache"]["content"]["zipball_url"] = $latest["zipball_url"];
                $data["cache"]["date"] = (new DateTime)->format("Y-m-d H:i:s");
                // Save the cache
                file_put_contents($updateFile, json_encode($data));

                return $data["cache"]["content"];
            }
        }

        // Checks if cache data exists
        private function hasCacheData() {
            global $updateFile;
            // Check if the file exists
            if(file_exists($updateFile)) {
                // Check if the file contains cache data
                $data = json_decode(file_get_contents($updateFile), true);
                if(isset($data["cache"])) {
                    return isset($data["cache"]["content"], $data["cache"]["date"]);
                } else {
                    return false;
                }
            } else {
                // Create a new file
                file_put_contents($updateFile, "{}");
                return false;
            }
        }

        // Checks if version data exists
        private function hasVersionData() {
            global $updateFile;
            // Check if the file exists
            if(file_exists($updateFile)) {
                // Check if the file contains version data
                $data = json_decode(file_get_contents($updateFile), true);
                if(isset($data["version"])) {
                    return isset($data["version"]["id"], $data["version"]["date"], $data["version"]["name"]);
                } else {
                    return false;
                }
            } else {
                // Create a new file
                file_put_contents($updateFile, "{}");
                return false;
            }
        }
    }
