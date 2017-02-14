<?php

    use \Administro\Administro;

    $page = "404";

    if(isset($GLOBALS["requestedPage"])) {
        $page = $GLOBALS["requestedPage"];
    }

    $renderedPage = Administro::Instance()->pagemanager->renderPage($page);

    if($renderedPage === false) {
        // The page could not be found
        echo "404 Page not found!";
    } else {
        // Display the page
        echo $renderedPage;
    }
