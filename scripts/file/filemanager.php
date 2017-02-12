<?php

    namespace Administro\File;

    use \Administro\Administro;

    class FileManager {

        private $files;

        public function __construct() {
            $this->files = array();
        }

        // Registers a new file.
        public function registerfile($file, $permission) {
            $this->files[$file] = $permission;
        }

        // Loads the file
        public function processFile($path) {
            $permissions = array();
            // Generate permissions
            foreach(Administro::Instance()->configmanager->getConfiguration()["data"] as $f => $p) {
                foreach(glob(BASEDIR."data/$f", GLOB_BRACE) as $f2) {
                    $permissions[$f2] = $p;
                }
            }
            // Check if the file exists
            $file = BASEDIR."data/$path";
            // Check for permissions
            $user = Administro::Instance()->usermanager;
            if(isset($permissions[$file])) {
                $perm = $permissions[$file];
                if($perm != "none") {
                    if(!$user->hasPermission($perm)) {
                        die("Invalid permissions!");
                    }
                }
            } else {
                die("Invalid permissions!");
            }
            // Load file
            if(!file_exists($file)) {
                die("Invalid file!");
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $type = finfo_file($finfo, $file);
                finfo_close($finfo);
                header('Cache-Control: public');
                header('Content-Type: '.$type);
                header('Content-Length: '.filesize($file));
                die(file_get_contents($file));
            }
        }
    }
