<?php

    use \Administro\Form\FormUtils;

    $pagemanager = $administro->pagemanager;

    $page = $GLOBALS["AdministroAdminParams"][0];
    $pageData = $pagemanager->getPage($page);

    $parseToken = FormUtils::generateToken("parsemarkdown");
    $saveToken = FormUtils::generateToken("savepage");

    // Load all page files
    $imgFiles = "";
    $otherFiles = "";
    foreach ($pagemanager->getPageFiles($page) as $file) {
        // Check if the file is an image
        $link = $pagemanager->getFileLink($page, $file);
        $prefix = BASEPATH."pages/$page/files/";
        if(@is_array(getimagesize($link))){
            // The file is an image
            $imgFiles.="<img title='$file' src='$prefix$file'></img>";
        } else {
            // The file is not an image
            $otherFiles.="<a href='$prefix$file'>$file</a> | ";
        }
    }
    $otherFiles = substr($otherFiles, 0, strlen($otherFiles) - 3);
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<section class="floating-box page">
    <header class="title">
        Edit Page: <?php echo $pageData["display"]; ?>
    </header>
    <section class="editor">
        <textarea id="editor"><?php echo $pagemanager->getPageContent($page); ?></textarea>
    </section>
    <section class="files">
        <header class="title sub">Image Files</header>
        <?php echo $imgFiles; ?>
        <header class="title sub">Other Files</header>
        <?php echo $otherFiles; ?>
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
        status: ["lines", "words"],
        toolbar: ["bold", "italic", "heading", "|",
            "quote", "unordered-list", "ordered-list", "|",
            "link", "image", "table", "|",
            "preview", "side-by-side", {
                name: "fullscreen",
                action: function fullscreen(editor){
                    SimpleMDE.toggleFullScreen(editor);
                    if(simplemde.isFullscreenActive()) {
                        // Fix the CodeMirror style
                        document.getElementsByClassName("CodeMirror-scroll")[0].style.height = "100%";
                    } else {
                        // Apply the custom CodeMirror style
                        document.getElementsByClassName("CodeMirror-scroll")[0].style.height = "auto";
                    }
                },
                className: "fa fa-arrows-alt no-disable no-mobile",
                title: "Toggle Fullscreen (F11)",
            }, "|",
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
