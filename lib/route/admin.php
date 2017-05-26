<?php

    function adminroute($administro) {
        if($administro->hasPermission('admin.access')) {
            $GLOBALS['administro'] = $administro;
            // Load main page
            require_once $administro->rootDir . '/lib/admin/main.php';
        } else {
            $administro->redirect('', 'bad/You can not access this page!');
        }
    }
