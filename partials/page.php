<?php

    require_once BASEDIR."scripts/pagemanager.php";

    $page = "404";
    
    if(isset($GLOBALS["requestedPage"])) {
        $page = $GLOBALS["requestedPage"];
    }

    $renderedPage = PageManager::renderPage($page);

    if($renderedPage === false) {
        // The page could not be found
        echo "404 OH NO NOT THIS AGAIN!";
    } else {
        // Display the page
        echo $renderedPage;
    }

?>
