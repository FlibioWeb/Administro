<?php

    namespace Administro\Page;

    use \Administro\Lib;
    use \Administro\Page;
    use \Administro\Config;
    use \Administro\Administro;

    require_once BASEDIR."vendor/twig/lib/Twig/Autoloader.php";

    class PageManager {

        private static $blacklist = array("admin", "form");

        public static function getPages() {
            // Make folder if it does not exist
            if(!is_dir(BASEDIR."pages")) {
                mkdir(BASEDIR."pages");
            }
            // Initialize array
            $pages = array();
            // Make sure data file exists
            if(file_exists(BASEDIR."pages/data.yaml")) {
                // Read the data file
                $data = \Administro\Lib\Spyc::YAMLLoad(BASEDIR."pages/data.yaml");
                // Loop through the data
                foreach ($data as $page => $pageData) {
                    // Verify page data contains required fields and content exists
                    if(file_exists(BASEDIR."pages/$page/content.md") && isset($pageData["display"], $pageData["hidden"], $pageData["template"], $pageData["permission"])) {
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

        public static function getPageContent($page) {
            // Attempt to load page data
            $pageData = self::getPage($page);
            if($pageData !== false) {
                return file_get_contents(BASEDIR."pages/$page/content.md");
            }
            return false;
        }

        public static function savePageContent($page, $content) {
            // Make sure the page exists
            if(self::pageExists($page)) {
                // Save the file
                file_put_contents(BASEDIR."pages/$page/content.md", $content);
                return true;
            }
            return false;
        }

        public static function renderPage($page, $forcedContent = false) {
            // Attempt to load page data
            $pageData = self::getPage($page);
            if($pageData !== false) {
                // Make sure template exists
                if(TemplateManager::templateExists($pageData["template"])) {
                    // Load the content
                    if($forcedContent === false) {
                        $content = file_get_contents(BASEDIR."pages/$page/content.md");
                    } else {
                        $content = $forcedContent;
                    }
                    // Parse the content
                    $content = (new \Administro\Lib\Parsedown)->text($content, $page);
                    // Setup variables
                    $variables = array("sitetitle" => \Administro\Config\ConfigManager::getConfiguration()["name"], "page" => $pageData["display"], "basepath" => BASEPATH, "goodmessage" => (isset($_SESSION["message-good"]) ? $_SESSION["message-good"] : ""), "badmessage" => (isset($_SESSION["message-bad"]) ? $_SESSION["message-bad"] : ""));
                    unset($_SESSION["message-good"]);
                    unset($_SESSION["message-bad"]);
                    // Replace variables
                    foreach ($variables as $key => $value) {
                        $content = str_ireplace("{{ ".$key." }}", $value, $content);
                        $content = str_ireplace("{{".$key."}}", $value, $content);
                    }
                    // Replace handlers
                    $pluginmanager = Administro::Instance()->pluginmanager;
                    foreach ($pluginmanager->handlers as $handler) {
                        $content = str_ireplace("[[ ".$handler." ]]", $pluginmanager->callHandler($handler), $content);
                        $content = str_ireplace("[[".$handler."]]", $pluginmanager->callHandler($handler), $content);
                    }
                    // Push content variable
                    $variables["content"] = $content;
                    // Setup Twig
                    \Twig_Autoloader::register();
                    $loader = new \Twig_Loader_Filesystem(BASEDIR."templates");
                    $twig = new \Twig_Environment($loader, array(
                        'cache' => BASEDIR."cache/twig",
                        'autoescape' => false,
                    ));
                    // Render the page
                    return $twig->render($pageData["template"].".html.twig", $variables);
                }
            }
            return false;
        }

    }
