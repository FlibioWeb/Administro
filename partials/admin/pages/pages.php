<?php

    $pagemanager = $administro->pagemanager;

?>
<section class="floating-box pages">
    <ul>
        <?php
            foreach ($pagemanager->getPages() as $page => $data) {
                echo "<li><a href='".BASEPATH."admin/pages/$page'>".$data["display"]."</a></li>";
            }
        ?>
    </ul>
</section>
