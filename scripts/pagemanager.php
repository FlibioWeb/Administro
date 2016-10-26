<?php

    require_once "spyc.php";
    require_once "templatemanager.php";
    require_once "configmanager.php";
    require_once "parsedown.php";

    $baseDir = dirname(__DIR__)."/";

    class PageManager {

        private static $blacklist = array("admin", "login", "logout", "404");

        public static function getPages() {
            global $baseDir;
            // Make folder if it does not exist
            if(!is_dir($baseDir."pages")) {
                mkdir($baseDir."pages");
            }
            // Initialize array
            $pages = array();
            // Make sure data file exists
            if(file_exists($baseDir."pages/data.yaml")) {
                // Read the data file
                $data = Spyc::YAMLLoad($baseDir."pages/data.yaml");
                // Loop through the data
                foreach ($data as $page => $pageData) {
                    // Verify page data contains required fields and content exists
                    if(file_exists($baseDir."pages/$page/content.md") && isset($pageData["display"], $pageData["hidden"], $pageData["template"], $pageData["permission"])) {
                        $pages[$page] = $pageData;
                    }
                }
            }
            // Return pages
            return $pages;
        }

        public static function pageExists($page) {
            return self::isOnBlacklist($page) || isset(self::getPages()[$page]);
        }

        public static function isOnBlacklist($page) {
            return in_array($page, self::$blacklist);
        }

        public static function getPage($page) {
            // Load all pages
            $pages = self::getPages();
            // Make sure the page exists
            if(isset($pages[$page])) {
                // Return the data
                return $pages[$page];
            }
            return false;
        }

        public static function renderPage($page) {
            global $baseDir;
            // Attempt to load page data
            $pageData = self::getPage($page);
            if($pageData !== false) {
                // Attempt to load template
                $template = TemplateManager::getTemplateContent($pageData["template"]);
                if($template !== false) {
                    // Load the content
                    $content = file_get_contents($baseDir."pages/$page/content.md");
                    // Parse the content
                    $content = (new Parsedown)->text($content);
                    // Setup variables
                    $variables = array("sitetitle" => ConfigManager::getConfiguration()["name"], "page" => $pageData["display"], "content" => $content, "basepath" => BASEPATH);
                    // Render page
                    foreach ($variables as $key => $value) {
                        $template = str_ireplace("{{ ".$key." }}", $value, $template);
                        $template = str_ireplace("{{".$key."}}", $value, $template);
                    }
                    return $template;
                }
            }
            return false;
        }

    }
