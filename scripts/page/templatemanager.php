<?php

    namespace Administro\Page;

    class TemplateManager {

        public static function templateExists($template) {
            // Make sure the folder exists
            if(!is_dir(BASEDIR."templates")) {
                mkdir(BASEDIR."templates");
            }
            // Check if the template exists
            return file_exists(BASEDIR."templates/$template.html.twig");
        }

    }
