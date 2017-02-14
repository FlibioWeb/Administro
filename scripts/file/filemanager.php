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

        private function setPerms($dir, $perm, $glob = true) {
            $perms = array();
            if($glob) {
                $scan = glob($dir, GLOB_BRACE);
            } else {
                $scan = scandir($dir);
            }
            foreach($scan as $f2) {
                if($f2 == "." || $f2 == "..") continue;
                // Check if directory
                $file = $f2;
                if(!$glob) {
                    $file = $dir."/".$f2;
                }
                if(is_dir($file)) {
                    $perms = array_merge($perms, $this->setPerms($file, $perm, false));
                } else {
                    // Add
                    $perms[$file] = $perm;
                }
            }
            return $perms;
        }

        // Loads the file
        public function processFile($path) {
            $permissions = array();
            // Generate permissions
            foreach(Administro::Instance()->configmanager->getConfiguration()["data"] as $f => $p) {
                $permissions = array_merge($permissions, $this->setPerms(BASEDIR."data/$f", $p));
            }
            foreach($this->files as $f => $p) {
                $permissions = array_merge($permissions, $this->setPerms(BASEDIR."data/$f", $p));
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
                $type = $this->getMime($file);
                header('Cache-Control: public');
                header('Content-Type: '.$type);
                header('Content-Length: '.filesize($file));
                die(file_get_contents($file));
            }
        }

        private function getMime($filename) {

            $mime_types = array(

                'txt' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'swf' => 'application/x-shockwave-flash',
                'flv' => 'video/x-flv',

                // images
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'svgz' => 'image/svg+xml',

                // archives
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'exe' => 'application/x-msdownload',
                'msi' => 'application/x-msdownload',
                'cab' => 'application/vnd.ms-cab-compressed',

                // audio/video
                'mp3' => 'audio/mpeg',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',

                // adobe
                'pdf' => 'application/pdf',
                'psd' => 'image/vnd.adobe.photoshop',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',

                // ms office
                'doc' => 'application/msword',
                'rtf' => 'application/rtf',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',

                // open office
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            );

            $ext = strtolower(array_pop(explode('.',$filename)));
            if (array_key_exists($ext, $mime_types)) {
                return $mime_types[$ext];
            }
            elseif (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME);
                $mimetype = finfo_file($finfo, $filename);
                finfo_close($finfo);
                return $mimetype;
            }
            else {
                return 'application/octet-stream';
            }
        }

    }
