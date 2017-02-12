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
                    if(file_exists(BASEDIR."pages/$page/content.md") && is_dir(BASEDIR."pages/$page/files") && isset($pageData["display"], $pageData["hidden"], $pageData["template"], $pageData["permission"], $pageData["priority"])) {
                        $pages[$page] = $pageData;
                    }
                }
            }
            // Return pages
            return $pages;
        }

        public static function getOrder() {
            $pages = self::getPages();
            $order = array();
            foreach ($pages as $id => $data) {
                if(!$data["hidden"]) {
                    $order[$id] = $data["priority"];
                }
            }
            asort($order);
            $final = array();
            foreach ($order as $key => $value) {
                $final[$key]["display"] = $pages[$key]["display"];
                $final[$key]["id"] = $key;
            }
            return $final;
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

        public static function getPageFiles($page) {
            // Make sure the page exists
            if(self::pageExists($page)) {
                // Load all files
                $files = array();
                foreach (scandir(BASEDIR."pages/$page/files") as $file) {
                    if($file != "." && $file != ".." && !is_dir(BASEDIR."pages/$page/files/$file")) {
                        array_push($files, $file);
                    }
                }
                return $files;
            }
            return false;
        }

        public static function getFileLink($page, $file) {
            return BASEDIR."pages/$page/files/$file";
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

        public static function renderPage($page, $forcedContent = false, $useTwig = true) {
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
                    $config = Administro::Instance()->configmanager->getConfiguration();
                    $um = Administro::Instance()->usermanager;
                    // Load all pages
                    $pages = self::getOrder();
                    // Parse the content
                    $content = (new \Administro\Lib\Parsedown)->text($content, $page);
                    // Setup variables
                    $variables["sitetitle"] = $config["name"];
                    $variables["pageid"] = $page;
                    $variables["currentpage"] = $pageData["display"];
                    $variables["basepath"] = BASEPATH;
                    $variables["goodmessage"] = (isset($_SESSION["message-good"]) ? $_SESSION["message-good"] : "");
                    $variables["badmessage"] = (isset($_SESSION["message-bad"]) ? $_SESSION["message-bad"] : "");
                    $variables["loggedin"] = $um->isLoggedIn();
                    $variables["admin"] = $um->hasPermission("admin.view");
                    $variables["user"] = "";
                    if($um->isLoggedIn()) {
                        $variables["user"] = $um->getUser()["display"];
                    }
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
                    if(!$useTwig) {
                        return $content;
                    }
                    // Push new variables
                    $variables["content"] = $content;
                    $variables["pages"] = $pages;
                    $variables["rand"] = microtime();
                    // Setup Twig
                    \Twig_Autoloader::register();
                    $loader = new \Twig_Loader_Filesystem(BASEDIR."templates");
                    $twig = new \Twig_Environment($loader, array(
                        'cache' => BASEDIR."cache/twig",
                        'autoescape' => false,
                    ));
                    // Render the page
                    $rendered = $twig->render($pageData["template"].".html.twig", $variables);
                    // Replace handlers in rendered
                    foreach ($pluginmanager->handlers as $handler) {
                        $rendered = str_ireplace("[[ ".$handler." ]]", $pluginmanager->callHandler($handler), $rendered);
                        $rendered = str_ireplace("[[".$handler."]]", $pluginmanager->callHandler($handler), $rendered);
                    }
                    return $rendered;
                }
            }
            return false;
        }

    }
