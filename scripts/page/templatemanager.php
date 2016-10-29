<?php

    namespace Administro\Page;

    class TemplateManager {

        public static function templateExists($template) {
            // Make sure the folder exists
            if(!is_dir(BASEDIR."templates")) {
                mkdir(BASEDIR."templates");
            }
            // Check if the template exists
            return file_exists(BASEDIR."templates/$template.html");
        }

        public static function getTemplateContent($template) {
            // Make sure the template exists
            if(self::templateExists($template)) {
                // Load the template
                return file_get_contents(BASEDIR."templates/$template.html");
            }
            return false;
        }

    }
