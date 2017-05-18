<?php

use Symfony\Component\Yaml\Yaml;

class Administro {

    var $rootDir, $baseDir;

    // Predefined variables
    var $configDir = "config/";
    var $reservedRoutes = array(
        'admin' => 'lib/route/admin.php',
        'login' => 'lib/route/login.php',
        'logout' => 'lib/route/logout.php',
        'form' => 'lib/route/form.php',
        'file' => 'lib/route/file.php'
    );
    var $forms = array(
        'login'
    );

    // Loaded objects
    var $config, $params, $pages, $viewPages, $plugins, $users, $reservedRoute, $currentPage, $variables;

    public function __construct($rootDir) {
        $this->rootDir = rtrim($rootDir, '/\\') . '/';
        $this->baseDir = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
    }

    public function run() {
        //$s = microtime(true);
        // Load any plugins
        $this->loadPlugins();
        // Load the configuration file
        $this->loadConfig();
        // Load users
        $this->loadUsers();
        // Load user data
        $this->loadUserData();
        // Parse the route
        $this->parseRoute();
        // Check if a page or a reserved route is being loaded
        if($this->reservedRoute) {
            // Load a route
            require_once $this->rootDir . '/' . $this->reservedRoutes[$this->params[0]];
            call_user_func_array($this->params[0] . 'route', array($this));
        } else {
            // Page load event
            $this->callEvent('onLoadingPages');
            // Load all pages
            $this->loadPages();
            // Load the correct page
            $this->loadPage();
        }
        //echo "<br>".((microtime(true) - $s) / 1000);
    }

    private function loadConfig() {
        // Default configuration
        $defaultConfig = array(
            'title' => 'Administro Site',
            'theme' => 'default',
            'default-page' => 'home',
            'default-template' => 'index'
        );
        // Check if the config exists
        if(file_exists($this->configDir . 'config.yaml')) {
            // Parse the config
            $this->config = Yaml::parse(file_get_contents($this->configDir . 'config.yaml')) + $defaultConfig;
        } else {
            // Use the default config
            file_put_contents($this->configDir . 'config.yaml', Yaml::dump($defaultConfig));
            $this->config = $defaultConfig;
        }
    }

    private function parseRoute() {
        $uri = substr(strtolower($_SERVER['REQUEST_URI']), strlen($this->baseDir));
        if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
        $request = '/' . trim($uri, '/');
        $this->params = array_values(array_filter(explode("/", $request)));
        if(count($this->params) >= 1) {
            // Check if this is a reserved route
            $this->reservedRoute = isset($this->reservedRoutes[$this->params[0]]);
        }
    }

    public function loadPages() {
        // Create the array
        $this->pages = array();
        $this->hiddenPages = array();
        // Load all pages
        foreach(scandir($this->rootDir . 'pages') as $f) {
            if(substr($f, 0, 1) === '.' || !file_exists($this->rootDir . 'pages/' . $f . '/content.md') || $f === '404') continue;
            // Parse the page content
            $raw = file_get_contents($this->rootDir . 'pages/' . $f . '/content.md');
            preg_match('/---(.*?)---/s', $raw, $matches);
            if(count($matches) !== 2) continue;
            // Read the YAML header
            $head = array_change_key_case(Yaml::parse($matches[1]));
            $title = $head['title'];
            $template = isset($head['template']) ? $head['template'] : $this->config['default-template'];
            $permission = isset($head['permission']) ? $head['permission'] : null;
            $priority = isset($head['priority']) ? $head['priority'] : 0;
            $id = strtolower(str_replace(' ', '', $title));
            // Read the page content
            $rawContent = trim(str_ireplace($matches[0], '', $raw));
            // Save the page
            $page = array(
                'id' => $id,
                'title' => $title,
                'template' => $template,
                'permission' => $permission,
                'priority' => $priority,
                'rawContent' => $rawContent
            );
            $this->pages[$id] = $page;
            if($priority >= 0 && $this->hasPermission($permission)) {
                $this->viewPages[$id] = $page;
            }
        }
        // Attempt to load the 404 page
        if(file_exists($this->rootDir . 'pages/404.md')) {
            $page404 = array(
                'id' => '404',
                'title' => '404',
                'template' => $this->config['default-template'],
                'priority' => -1,
                'rawContent' => file_get_contents($this->rootDir . 'pages/404.md')
            );
        } else {
            $page404 = array(
                'id' => '404',
                'title' => '404',
                'template' => $this->config['default-template'],
                'priority' => -1,
                'rawContent' => "404 Error - Page not found"
            );
        }
        $this->pages['404'] = $page404;
        // Sort pages
        uasort($this->pages, function($p1, $p2) {
            $c1 = $p1['priority'];
            $c2 = $p2['priority'];
            if($c1 < $c2) return -1;
            if($c1 == $c2) return 0;
            if($c1 > $c2) return 1;
        });
    }

    private function loadPage() {
        // Check if default page
        $pageId;
        if(count($this->params) === 0) {
            // Load default page
            $pageId = $this->config['default-page'];
        } else {
            // Check if the page exists
            $p0 = strtolower($this->params[0]);
            if(isset($this->pages[$p0])) {
                $pageId = $p0;
                if($pageId === $this->config['default-page']) {
                    // Redirect
                    header('Location: ' . $this->baseDir);
                    die();
                }
            } else {
                // Page not found
                $pageId = '404';
            }
        }
        $this->currentPage = $this->pages[$pageId];
        // Verify permission
        if(!$this->hasPermission(isset($this->currentPage['permission']) ? $this->currentPage['permission'] : '')) {
            // User can not view the page
            $this->currentPage['rawContent'] = 'You do not have permission to view this page!';
            $this->currentPage['template'] = $this->config['default-template'];
        }
        // Parse the page
        $parsedContent = (new AdministroParsedown($this))->text($this->currentPage['rawContent']);
        // Replace custom variables
        foreach($this->variables as $k => $v) {
            $parsedContent = str_ireplace('[[ ' . $k . ' ]]', $v, $parsedContent);
        }
        // Render the page
        $this->renderPage($this->currentPage, $parsedContent);
    }

    public function renderPage($page, $content) {
        // Load Twig
        $twigLoader = new Twig_Loader_Filesystem('themes/' . $this->config['theme']);
        $twig = new Twig_Environment($twigLoader, array('autoescape' => false));
        // Display the page
        echo $twig->render($page['template'] . '.twig', array(
            'content' => $content,
            'site_title' => $this->config['title'],
            'current_page' => $page,
            'pages' => $this->viewPages,
            'logged_in' => isset($_SESSION['user']),
            'theme_url' => $this->baseDir . 'themes/' . $this->config['theme'],
            'base_dir' => $this->baseDir,
            'user' => isset($_SESSION['user']) ? $_SESSION['user'] : array()
        ));
    }

    private function loadPlugins() {
        // Setup array
        $this->plugins = array();
        // Search all folders in the plugins directory
        foreach(scandir($this->rootDir . 'plugins') as $f) {
            if(substr($f, 0, 1) === '.' || !is_dir($this->rootDir . 'plugins/' . $f)) continue;
            // Load the plugin file
            require_once $this->rootDir . 'plugins/' . $f . '/plugin.php';
            // Initialize the plugin
            $class = $f . 'Plugin';
            $plugin = new $class($this);
            // Add the plugin
            array_push($this->plugins, $plugin);
        }
    }

    private function loadUsers() {
        if(file_exists($this->configDir . 'users.yaml')) {
            // Parse the users
            $this->users = Yaml::parse(file_get_contents($this->configDir . 'users.yaml'));
        } else {
            // Use the default config
            file_put_contents($this->configDir . 'users.yaml', Yaml::dump(array()));
            $this->users = array();
        }
    }

    private function loadUserData() {
        if(isset($_SESSION['user'])) {
            // Update data
            $_SESSION['user'] = $this->users[$_SESSION['user']['id']];
        }
    }

    public function createUser($username, $password, $permissions = array()) {
        // Get users
        $users = $this->users;
        // Generate id
        $id = strtolower($username);
        if(isset($users[$id])) {
            return false;
        }
        // Create user data
        $user = array(
            'id' => $id,
            'name' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 11]),
            'permissions' => $permissions
        );
        // Save the user
        $users[$id] = $user;
        file_put_contents($this->configDir . 'users.yaml', Yaml::dump($users));
        return true;
    }

    public function login($username, $password) {
        if(!isset($_SESSION['user'])) {
            // Get users
            $users = $this->users;
            // Check if user exists
            $id = strtolower($username);
            if(isset($users[$id])) {
                // Verify password
                if(password_verify($password, $users[$id]['password'])) {
                    // Success
                    unset($users[$id]['password']);
                    $_SESSION['user'] = $users[$id];
                    return true;
                }
            }
        }
        return false;
    }

    public function hasPermission($permission) {
        // Check if the permission is empty
        if(empty($permission)) return true;
        // Make sure the user is set
        if(isset($_SESSION['user'])) {
            $permissions = $_SESSION['user']['permissions'];
            // Check the permission type
            $split = explode(".", $permission);
            if(count($split) == 1) {
                // Simple permission
                return in_array($permission, $permissions);
            } else if(count($split) == 2) {
                // Advanced permission
                $perm1 = $split[0];
                return (in_array($permission, $permissions) || in_array($perm1.".super", $permissions));
            }
        }
        return false;
    }

    public function redirect($location, $message = '') {
        header('Location: ' . $this->baseDir . $location);
        die();
    }

    public function generateNonce($formName) {
        $nonce = md5(microtime());
        $_SESSION['nonce-' . $formName] = $nonce;
        return $nonce;
    }

    public function verifyNonce($formName, $nonce) {
        $resp = (isset($_SESSION['nonce-' . $formName]) ? $_SESSION['nonce-' . $formName] : '' === $nonce);
        unset($_SESSION['nonce-' . $formName]);
        return $resp;
    }

    public function verifyParameters($formName, $params) {
        // Verify nonce
        if(isset($_POST['nonce']) && $this->verifyNonce($formName, $_POST['nonce'])) {
            // Check parameters
            $verified = array();
            foreach($params as $param) {
                if(!isset($_POST[$param])) {
                    return false;
                }
                $verified[$param] = $_POST[$param];
            }
            return $verified;
        } else {
            return false;
        }
    }

    public function callEvent($event) {
        foreach($this->plugins as $plugin) {
            $plugin->callEvent($event);
        }
    }
}
