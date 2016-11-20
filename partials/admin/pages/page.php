<?php

    use \Administro\Form\FormUtils;

    $pagemanager = $administro->pagemanager;

    $page = $GLOBALS["AdministroAdminParams"][0];
    $pageData = $pagemanager->getPage($page);

    $parseToken = FormUtils::generateToken("parsemarkdown");

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<section class="floating-box pages">
    <header class="title">
        Edit Page: <?php echo $pageData["display"]; ?>
    </header>
    <section>
        <textarea id="editor"><?php echo $pagemanager->getPageContent($page); ?></textarea>
    </section>
</section>
<script>
    var cached = "Loading...";

    var simplemde = new SimpleMDE({
        previewRender: function(plainText, preview) {
            var xmlhttp = new XMLHttpRequest();
            var params = "page=<?php echo $page; ?>&token=<?php echo $parseToken; ?>&content="+plainText;
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    if(this.responseText !== null) {
                        preview.innerHTML = this.responseText;
                        cached = this.responseText;
                    }
                }
            };
            xmlhttp.open("POST", "<?php echo BASEPATH; ?>form/parsemarkdown", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(params);
            return cached;
        }
    });
</script>
