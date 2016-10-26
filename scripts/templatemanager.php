<?php

    $baseDir = dirname(__DIR__)."/";

    class TemplateManager {

        public static function templateExists($template) {
            global $baseDir;
            // Make sure the folder exists
            if(!is_dir($baseDir."templates")) {
                mkdir($baseDir."templates");
            }
            // Check if the template exists
            return file_exists($baseDir."templates/$template.html");
        }

        public static function getTemplateContent($template) {
            global $baseDir;
            // Make sure the template exists
            if(self::templateExists($template)) {
                // Load the template
                return file_get_contents($baseDir."templates/$template.html");
            }
            return false;
        }

    }
