<?php

    namespace Administro;

    use \Administro\Config\ConfigManager;
    use \Administro\Page\PageManager;
    use \Administro\Page\TemplateManager;
    use \Administro\Plugin\PluginManager;

    final class Administro {

        var $configmanager;
        var $pagemanager;
        var $templatemanager;
        var $pluginmanager;

        // Construction
        private function __construct() {
            $this->configmanager = new ConfigManager;
            $this->pagemanager = new PageManager;
            $this->templatemanager = new TemplateManager;
            $this->pluginmanager = new PluginManager;
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
