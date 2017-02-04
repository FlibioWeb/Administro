<?php

    namespace Administro\Config;

    class FileUtils {

        // Deletes a folder
        public static function deleteFolder($folder) {
            // Loop through all files
            foreach (scandir($folder) as $file) {
                // Make sure the file is not a navigation link
                if($file != "." && $file != "..") {
                    // Check if it is a directory
                    if(is_dir($folder."/$file")) {
                        // Delete the folder
                        self::deleteFolder($folder."/$file");
                    } else {
                        // Delete the file
                        unlink($folder."/$file");
                    }
                }
            }
            // Delete the folder
            rmdir($folder);
        }
    }
