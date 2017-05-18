<?php

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
