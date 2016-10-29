<?php

    namespace Administro;

    use \Administro\Config\ConfigManager;
    use \Administro\Page\PageManager;
    use \Administro\Page\TemplateManager;
    use \Administro\Page\PartialManager;
    use \Administro\Plugin\PluginManager;
    use \Administro\Route\RouteManager;
    use \Administro\User\UserManager;

    final class Administro {

        var $configmanager;
        var $pagemanager;
        var $templatemanager;
        var $partialmanager;
        var $pluginmanager;
        var $routemanager;
        var $usermanager;

        // Construction
        private function __construct() {
            $this->configmanager = new ConfigManager;
            $this->pagemanager = new PageManager;
            $this->templatemanager = new TemplateManager;
            $this->partialmanager = new PartialManager;
            $this->pluginmanager = new PluginManager;
            $this->routemanager = new RouteManager;
            $this->usermanager = new UserManager;
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
