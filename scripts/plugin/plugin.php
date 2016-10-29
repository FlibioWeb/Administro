<?php

    namespace Administro\Plugin;

    abstract class Plugin {

        var $handlers = array();

        // Gets the handlers
        public function getHandlers() {
            return $this->handlers;
        }

        // Registers a handler
        public function registerHandler($name, $function) {
            $this->handlers[$name] = $function;
        }

        // Gets the plugin id
        public function getId() {
            return strtolower(str_replace(" ", "_", $this->getName()));
        }

        // Gets the name of the plugin
        abstract function getName();

    }
