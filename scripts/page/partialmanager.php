<?php

    namespace Administro\Page;

    class PartialManager {

        var $partials;

        public function __construct() {
            // Initialize the array
            $p = BASEDIR."partials/";
            $this->partials = array("page" => $p."page.php", "admin" => $p."admin/admin.php", "login" => $p."login.php");
        }

        // Registers a new partial
        public function registerPartial($partial, $plugin, $file) {
            $this->partials[$partial] = BASEDIR."plugins/$plugin/partials/$file";
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
