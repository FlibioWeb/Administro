<?php

    use \Administro\Administro;
    use \Administro\Form\FormUtils;

    // Load Administro
    $administro = Administro::Instance();
    $usermanager = $administro->usermanager;
    $adminroutes = $administro->adminroutes;
    $adminpartials = $administro->adminpartials;

    // Load page data
    $currentPage = $GLOBALS["AdministroAdminPage"];
    $currentRoute = $GLOBALS["AdministroAdminRoute"];

    // Check if the user has permisison to continue
    if(!$usermanager->isLoggedIn()) {
        // Redirect to a login screen
        header("Location: ".BASEPATH."login");
        die("Redirecting...");
    } else if(!$usermanager->hasPermission("admin.".$currentPage)) {
        // Redirect to default page
        header("Location: ".BASEPATH);
        die("Redirecting...");
    }

    // Load messages
    $good = (isset($_SESSION["message-good"]) ? $_SESSION["message-good"] : "");
    unset($_SESSION["message-good"]);
    $bad = (isset($_SESSION["message-bad"]) ? $_SESSION["message-bad"] : "");
    unset($_SESSION["message-bad"]);

    // Display Variables
    $siteName = $administro->configmanager->getConfiguration()["name"];
    $user = $usermanager->getUser()["display"];
?>
<head>
    <title>Admin | <?php echo $siteName ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>partials/style/admin.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
    <aside class="sidebar">
        <header class="header">
            Administration Panel
        </header>
        <section class="user">
            Welcome, <?php echo $user; ?>
            <p>
            <a href="<?php echo BASEPATH; ?>logout"><i class="fa fa-sign-out"></i> Logout</a>
        </section>
        <nav class="nav">
            <ul>
                <?php
                    // Display all admin pages
                    foreach ($adminroutes->getAdminRoutes() as $route) {
                        if($route->isVisible()) {
                            echo "<li><a href=\"".BASEPATH."admin/".$route->getPartial()."\"><i class=\"fa fa-".$route->getIcon()."\"></i> ".$route->getName()."</a></li>";
                        }
                    }
                ?>
            </ul>
        </nav>
        <footer class="footer">
            <a href="<?php echo BASEPATH; ?>"><i class="fa fa-angle-left"></i> Back to Site</a>
        </footer>
    </aside>
    <main class="main">
        <header class="header">
            <?php echo $currentRoute->getName(); ?>
        </header>
        <main class="board">
            <?php require_once $adminpartials->getPartial($currentPage); ?>
        </main>
        <section id="message" class="message"></section>
    </main>
</body>
<script>
    function displayMessage(type, content) {
        var message = document.getElementById("message");
        if(content == "" || !message.innerHTML == "") return;
        // Load the message
        message.innerHTML = content;
        // Set the type
        if(type) {
            message.className += " good";
        } else {
            message.className += " bad";
        }
        message.style.opacity = 1;
        // Start exit transition
        setTimeout(function(){
            message.style.opacity = 0;
        }, 3000);
        // Clear the message
        setTimeout(function(){
            message.innerHTML = "";
            message.className = "message";
        }, 3500);
    }
    // Display loaded message
    displayMessage(true, <?php echo json_encode($good, JSON_HEX_TAG); ?>);
    displayMessage(false, <?php echo json_encode($bad, JSON_HEX_TAG); ?>);
</script>
