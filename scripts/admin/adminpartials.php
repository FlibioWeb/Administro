<?php

    namespace Administro\Admin;

    class AdminPartials {

        var $partials;

        public function __construct() {
            // Initialize the array
            $p = BASEDIR."partials/admin/pages/";
            $this->partials = array("home" => $p."home.php", "pages" => $p."pages.php", "page" => $p."page.php");
        }

        // Registers a new partial
        public function registerPartial($partial, $plugin, $file) {
            $this->partials[$partial] = BASEDIR."plugins/$plugin/partials/admin/$file";
        }

        // Checks if a partial exists
        public function partialExists($partial) {
            return isset($this->partials[$partial]);
        }

        // Gets a specific partial
        public function getPartial($partial) {
            if($this->partialExists($partial)) {
                return $this->partials[$partial];
            }
            return false;
        }

        // Gets all partials
        public function getPartials() {
            return $this->partials;
        }

    }
