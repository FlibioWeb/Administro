<?php

    namespace Administro;

    use \Administro\Config\ConfigManager;
    use \Administro\Page\PageManager;
    use \Administro\Page\TemplateManager;

    final class Administro {

        var $configmanager;
        var $pagemanager;
        var $templatemanager;

        // Plugin Variables
        var $plugins;
        var $handlers;

        // Construction
        private function __construct() {
            $this->configmanager = new ConfigManager;
            $this->pagemanager = new PageManager;
            $this->templatemanager = new TemplateManager;

            $this->plugins = array();
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

        // Gets the Administro instance
        public static function Instance() {
            static $inst = null;
            if ($inst === null) {
                $inst = new Administro;
            }
            return $inst;
        }

    }