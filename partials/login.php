<?php

    use \Administro\Administro;
    use \Administro\Form\FormUtils;

    // Generate a form token
    $token = FormUtils::generateToken("login");
?>

<body>
    <form method="post" action="<?php echo BASEPATH."form/"; ?>login">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label>Username</label><p><input type="text" name="username">
        <p>
        <label>Password</label><p><input type="password" name="password">
        <p>
        <input type="submit" value="Login">
    </form>
</body>
