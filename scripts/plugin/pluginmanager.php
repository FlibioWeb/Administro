<?php

    namespace Administro\Plugin;

    class PluginManager {

        var $plugins;
        var $handlers;

        public function __construct() {
            $this->plugins = array();
            $this->handlers = array();

            // Generate the plugins directory if it does not exist
            if(!is_dir(BASEDIR."plugins")) {
                mkdir(BASEDIR."plugins");
            }
        }

        // Plugin Registration
        public function registerPlugin($plugin) {
            $this->plugins[$plugin->getId()] = $plugin;
            // Add the plugin's handlers
            foreach($plugin->getHandlers() as $handler => $function) {
                $this->handlers[$handler] = $plugin->getId();
            }
        }

        // Calls a handler
        public function callHandler($handler) {
            // Make sure the handler exists
            if(isset($this->handlers[$handler])) {
                // Determine the correct function
                $plugin = $this->plugins[$this->handlers[$handler]];
                $function = $plugin->getHandlers()[$handler];
                // Execute the function
                return $plugin->{$function}();
            }
            return false;
        }

        // Retrieve a plugin
        public function getPlugin($plugin) {
            if(isset($this->plugins[$plugin])) {
                return $this->plugins[$plugin];
            }
            return false;
        }

    }
