<?php

?>
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
