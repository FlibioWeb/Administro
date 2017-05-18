<?php

    function logoutroute($administro) {
        unset($_SESSION['user']);
        $administro->redirect('', 'good/Successfully logged out!');
    }
