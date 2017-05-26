<?php

    use Symfony\Component\Yaml\Yaml;

    function loginform($administro) {
        $params = $administro->verifyParameters('login', array('username', 'password'));
        if($params !== false) {
            // Make sure user is not logged in
            if(isset($_SESSION['user'])) {
                $administro->redirect('login', 'bad/You are already logged in!');
            }
            // Attempt to login
            if($administro->login($params['username'], $params['password'])) {
                // Success
                $administro->redirect('', 'good/Successfully logged in!');
            } else {
                // Failed to login
                $administro->redirect('login', 'bad/Failed to login!');
            }
        } else {
            $administro->redirect('login', 'bad/Invalid parameters!');
        }
    }

    function dropdownform($administro) {
        if(!isset($_POST['page'], $_POST['file'])) die('Invalid parameters!');
        header('Location: ' . $administro->baseDir . 'file/' . $_POST['page'] . '/' . $_POST['file']);
        die();
    }

    function adminconfigform($administro) {
        $params = $administro->verifyParameters('adminconfig', array('title'));
        if($params !== false) {
            // Verify permission
            if($administro->hasPermission('admin.config')) {
                // Save the data
                $administro->config['title'] = $params['title'];
                file_put_contents($administro->configDir . 'config.yaml', Yaml::dump($administro->config));
                $administro->redirect('admin/home', 'good/Saved configuration!');
            } else {
                $administro->redirect('admin/home', 'bad/You do not have permission!');
            }
        } else {
            $administro->redirect('login', 'bad/Invalid parameters!');
        }
    }

    function renderpageform($administro) {
        $params = $administro->verifyParameters('renderpage', array('content', 'page'), false);
        if($params !== false && $administro->hasPermission('admin.edit')) {
            echo $administro->parseMarkdown($params['content'], $params['page']);
        } else {
            die('Invalid parameters!');
        }
    }

    function savepageform($administro) {
        $params = $administro->verifyParameters('savepage', array('content', 'page', 'permission'));
        if($params !== false && $administro->hasPermission('admin.edit')) {
            // Load all pages
            $administro->loadPages();
            // Check if the page is set
            if(isset($administro->pages[$params['page']])) {
                $page = $administro->pages[$params['page']];
                // Write new head
                $head = Yaml::dump(array(
                    'title' => $page['title'],
                    'template' => $page['template'],
                    'permission' => $params['permission'],
                    'priority' => $page['priority']
                ));
                // Save the file
                file_put_contents($administro->rootDir . 'pages/' . $params['page'] . '/content.md',
                    '---' . PHP_EOL . $head . '---' . PHP_EOL . $params['content']);
                $administro->redirect('admin/page/' . $params['page'], 'good/Saved page!');
            } else {
                $administro->redirect('admin/pages', 'bad/Invalid page!');
            }
        } else {
            $administro->redirect('admin/pages', 'bad/Invalid parameters!');
        }
    }

    function uploadpagefileform($administro) {
        // Verify input
        if(isset($_POST['nonce'], $_POST['page']) && count($_FILES) == 1 && $administro->verifyNonce('uploadpagefile', $_POST['nonce']) &&
        $administro->hasPermission('admin.edit')) {
            $file = $administro->rootDir . 'pages/' . $_POST['page'] . '/files/' . basename($_FILES["file"]["name"]);
            // Make sure file does not exist
            if(file_exists($file)) {
                $administro->redirect('admin/page/' . $_POST['page'], 'bad/Invalid parameters!');
            }
            // Verify file size
            if ($_FILES["file"]["size"] > 10000000) {
                $administro->redirect('admin/page/' . $_POST['page'], 'bad/File must be under 10MB!');
            }
            // Upload the file
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $file)) {
                $administro->redirect('admin/page/' . $_POST['page'], 'good/File uploaded!');
            } else {
                $administro->redirect('admin/page/' . $_POST['page'], 'bad/Failed to upload file!');
            }
        } else {
            $administro->redirect('admin/pages', 'bad/Invalid parameters!');
        }
    }

    function updatecheckform($administro) {
        $params = $administro->verifyParameters('updatecheck', array());
        $context = stream_context_create(array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT'])));
        if($params !== false) {
            // Verify permission
            if($administro->hasPermission('admin.update')) {
                // Load latest update
                $latest = json_decode(file_get_contents("https://api.github.com/repos/FlibioWeb/Administro/releases/latest", false, $context), true);
                $update = array(
                    'version' => $latest['tag_name'],
                    'url' => $latest['zipball_url']
                );
                file_put_contents($administro->rootDir . 'latest_update.yaml', Yaml::dump($update));
                $administro->redirect('admin/home', '');
            } else {
                $administro->redirect('admin/home', 'bad/You do not have permission!');
            }
        } else {
            $administro->redirect('admin/home', 'bad/Invalid parameters!');
        }
    }

    function updateform($administro) {
        $params = $administro->verifyParameters('update', array());
        $context = stream_context_create(array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT'])));
        if($params !== false) {
            // Verify permission
            if($administro->hasPermission('admin.update')) {
                // Load latest update
                $latest = json_decode(file_get_contents("https://api.github.com/repos/FlibioWeb/Administro/releases/latest", false, $context), true);
                $url = $latest['zipball_url'];
                $version = $latest['tag_name'];
                // Install
                file_put_contents($administro->rootDir . 'administroinstall.zip', file_get_contents($url, false, $context));
                // Extract the zip file
                $zip = new ZipArchive;
                $res = $zip->open($administro->rootDir . "administroinstall.zip");
                if ($res === TRUE) {
                    $zip->extractTo($administro->rootDir);
                    $zip->close();
                    // Delete the zip file
                    unlink($administro->rootDir . "administroinstall.zip");
                    // Move the files
                    $destination = $administro->rootDir;
                    $from = glob($administro->rootDir . 'FlibioWeb-Administro-*/')[0];
                    moveFolder($destination, $from);
                    // Set version
                    $administro->config['version'] = $version;
                    file_put_contents($administro->configDir . 'config.yaml', Yaml::dump($administro->config));
                    // Remove old version file
                    @unlink($administro->rootDir . 'latest_update.yaml');
                    $administro->redirect('admin/home', 'good/Successfully updated!');
                } else {
                    $administro->redirect('admin/home', 'bad/Update failed!');
                }
            } else {
                $administro->redirect('admin/home', 'bad/You do not have permission!');
            }
        } else {
            $administro->redirect('admin/home', 'bad/Invalid parameters!');
        }
    }

    function moveFolder($destination, $from) {
        $toMove = scandir($from);
        // Loop through all files
        foreach ($toMove as $file) {
            // Make sure the file is not a navigation link
            if($file != "." && $file != "..") {
                // Check if it is a directory
                if(is_dir($destination.$file)) {
                    // Move the directory
                    moveFolder($destination.$file."/", $from.$file."/");
                } else {
                    // Move the file
                    rename($from.$file, $destination.$file);
                }
            }
        }
        // Delete the initial folder
        rmdir($from);
    }
