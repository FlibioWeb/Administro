<?php

    function formroute($administro) {
        // Load parameters
        $params = $administro->params;
        // Check if form exists
        if(in_array($params[1], $administro->forms)) {
            // Load default forms
            require_once $administro->rootDir . '/lib/administroforms.php';
            // Call the form
            call_user_func($params[1] . 'form', $administro);
        } else {
            die('Form not found!');
        }
    }
