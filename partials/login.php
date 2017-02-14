<?php

    use \Administro\Administro;
    use \Administro\Form\FormUtils;

    $administro = Administro::Instance();
    $title = $administro->configmanager->getConfiguration()["name"];

    if($administro->usermanager->isLoggedIn()) {
        // The user is already logged in
        $_SESSION["message-bad"] = "You are already logged in!";
        header("Location: ".BASEPATH);
        die("Redirecting...");
    }

    // Create messages
    $good = (isset($_SESSION["message-good"]) ? $_SESSION["message-good"] : "");
    unset($_SESSION["message-good"]);
    $bad = (isset($_SESSION["message-bad"]) ? $_SESSION["message-bad"] : "");
    unset($_SESSION["message-bad"]);

    // Generate a form token
    $token = FormUtils::generateToken("login");
?>
<head>
    <title>Login | <?php echo $title; ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo BASEPATH; ?>file/administro/style/main.css">
</head>
<body>
    <h3><?php echo $title; ?> Login</h3>
    <p>
    <form method="post" action="<?php echo BASEPATH."form/"; ?>login">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label>Username</label><p><input type="text" name="username">
        <p>
        <label>Password</label><p><input type="password" name="password">
        <p>
        <input type="submit" value="Login">
    </form>
    <p>
    <span class="good"><?php echo $good; ?></span>
    <p>
    <span class="bad"><?php echo $bad; ?></span>
</body>
