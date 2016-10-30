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

    $user = $usermanager->getUser()["display"];
    $siteName = $administro->configmanager->getConfiguration()["name"];
?>
<html>
    <title>Admin | <?php echo $siteName ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>partials/style/admin.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
</html>

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
                <li><a href="<?php echo BASEPATH ?>admin"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="<?php echo BASEPATH ?>admin/pages"><i class="fa fa-file-text"></i> Pages</a></li>
            </ul>
        </nav>
        <footer class="footer">
            <a href="<?php echo BASEPATH; ?>"><i class="fa fa-angle-left"></i> Back to Site</a>
        </footer>
    </aside>
    <main class="main">
        <header class="header">
            Home
        </header>
        <main class="board">
            Control Panel
        </main>
    </main>
</body>
