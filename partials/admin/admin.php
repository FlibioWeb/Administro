<?php

    use \Administro\Administro;

    $administro = Administro::Instance();
    $usermanager = $administro->usermanager;

    // Check if the user has permisison to continue
    if(!$usermanager->isLoggedIn()) {
        // Redirect to a login screen
        header("Location: ".BASEPATH."login");
        die("Redirecting...");
    } else if(!$usermanager->hasPermission("admin.view")) {
        // Redirect to default page
        header("Location: ".BASEPATH);
        die("Redirecting...");
    }

    $siteName = $administro->configmanager->getConfiguration()["name"];
?>
<html>
    <title><?php echo $siteName ?> Administration</title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>partials/admin/style/main.css">
</html>

<body>
    <h2><?php echo $siteName ?> Administration</h2>
    <p>

</body>
