<?php

    use \Administro\Administro;
    use \Administro\Form\FormUtils;

    $administro = Administro::Instance();
    $usermanager = $administro->usermanager;
    $updater = $administro->updater;

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

    // Set form tokens
    $updateToken = FormUtils::generateToken("update");

    $user = $usermanager->getUser()["display"];
    $siteName = $administro->configmanager->getConfiguration()["name"];
    // Administro Version
    $version = "N/A";
    $currentVersion = $updater->getCurrentVersion();
    if($currentVersion !== false) {
        $version = $currentVersion;
    }
    // Update Check
    $updateAvailable = $updater->checkForUpdate();
?>
<html>
    <title>Admin | <?php echo $siteName ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>partials/style/admin.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
</html>

<body>
    <?php require_once "nav.php"; ?>
    <main class="main">
        <header class="header">
            Home
        </header>
        <main class="board">
            <section class="maintenance">
                <header class="title">
                    Maintenance
                </header>
                <p>Current Version: <?php echo $version; ?></p>
                <section class="bottom">
                    <form class="form update" method="POST" action="<?php echo BASEPATH; ?>form/update">
                        <input type="hidden" name="token" value="<?php echo $updateToken; ?>">
                        <button type="submit" <?php if(!$updateAvailable) echo "style='display: none;'"; ?>><i class="fa fa-cloud-download"></i> Update</button>
                    </form>
                </section>
            </section>
        </main>
    </main>
</body>
