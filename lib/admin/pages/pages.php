<div class='title'>
    Pages
</div>
<div class='spacer'></div>
<div class='page_list'>
    <?php

        // Load all administro pages
        $administro->loadPages();

        // Display all pages
        foreach($administro->pages as $pageId => $pageData) {
            if($pageId === 404) continue;
            echo '<div><a href="' . $administro->baseDir . 'admin/page/' . $pageId . '">' . $pageData['title'] . '</a></div>';
        }

    ?>
</div>
