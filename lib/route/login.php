<?php

    function loginroute($administro) {
        // Verify the user is not already logged in
        if(isset($_SESSION['user'])) {
            // User can't be logged in
            $administro->redirect('', 'bad/You are already logged in!');
        }
        // Load pages for navigation
        $administro->loadPages();
        // Render the login form
        $administro->renderPage(array(
            'id' => 'login',
            'title' => 'Login',
            'template' => $administro->config['default-template'],
            'rawContent' => ''
        ),
        '<form action="' . $administro->baseDir . 'form/login" method="post" class="login-form">' .
        '<input type="hidden" name="nonce" value="' . $administro->generateNonce('login') . '">' .
        '<div><label>Username</label><input type="text" name="username"></div>' .
        '<div><label>Password</label><input type="password" name="password"></div>' .
        '<div><input type="submit" value="Login"></div>' .
        '</form>'
        );
    }
