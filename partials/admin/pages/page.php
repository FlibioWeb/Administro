<?php

    use \Administro\Form\FormUtils;

    $pagemanager = $administro->pagemanager;

    $page = $GLOBALS["AdministroAdminParams"][0];
    $pageData = $pagemanager->getPage($page);

    $parseToken = FormUtils::generateToken("parsemarkdown");
    $saveToken = FormUtils::generateToken("savepage");

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<section class="floating-box page">
    <header class="title">
        <header>Edit Page: <?php echo $pageData["display"]; ?></header>
        <nav>

        </nav>
    </header>
    <section class="editor">
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
        },
        toolbar: ["bold", "italic", "heading", "|",
            "quote", "unordered-list", "ordered-list", "|",
            "link", "image", "table", "|",
            "preview", "side-by-side", "fullscreen", "|",
            {
                name: "save",
                action: function savePage(editor){
                    var content = simplemde.value();
                    var xmlhttp = new XMLHttpRequest();
                    var params = "page=<?php echo $page; ?>&token=<?php echo $saveToken; ?>&content="+content;
                    xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            if(this.responseText) {
                                // Save successful
                                window.displayMessage(true, "Successfully saved the page content!");
                            } else {
                                // Save failed
                            }
                        }
                    };
                    xmlhttp.open("POST", "<?php echo BASEPATH; ?>form/savepage", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xmlhttp.send(params);
                },
                className: "fa fa-floppy-o",
                title: "Save",
            }
        ],
    });
</script>
