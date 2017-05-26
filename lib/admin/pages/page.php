<?php
    $params = $mainParams;

    // Verify params have a page
    if(count($params) !== 3) {
        $administro->redirect('admin/pages', 'bad/Invalid parameters!');
    }

    $pageId = $params[2];

    // Verify page exists
    $administro->loadPages();
    if($pageId == 404 || !isset($administro->pages[$pageId])) {
        $administro->redirect('admin/pages', 'bad/Page could not be found!');
    }

    // Load the page
    $page = $administro->pages[$pageId];

    // Load page files
    $fileImgHtml = '';
    $fileImgModal = '';
    $fileHtml = '';
    $fileModal = '';
    $pageFileDir = $administro->rootDir . '/pages/' . $pageId . '/files/';
    foreach(scandir($pageFileDir) as $file) {
        if(substr($file, 0, 1) === '.') continue;
        if(@is_array(getimagesize($pageFileDir . $file))) {
            // Image
            $fileImgHtml .= '<img title="' . $file . '" src="' . $administro->baseDir . 'file/' . $pageId . '/' . $file . '" />';
            $fileImgModal .= '<option value=' . $file . '>' . $file . '</option>';
        } else {
            // Not an image
            $fileHtml .= '<code><a href="' . $administro->baseDir . 'file/' . $pageId . '/' . $file . '">' . $file . '</a></code>';
            $fileModal .= '<option value=' . $file . '>' . $file . '</option>';
        }
    }

    // Generate nonce
    $pageRenderNonce = $administro->generateNonce('renderpage');
    $pageSaveNonce = $administro->generateNonce('savepage');
    $pageUploadNonce = $administro->generateNonce('uploadpagefile');
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<div class='title'>
    Page: <?php echo $page['title']; ?>
</div>
<div class='spacer'></div>
<div>
    <form method='post' action='<?php echo $administro->baseDir . 'form/savepage' ?>' name='save_page'>
        <input type='hidden' name='nonce' value='<?php echo $pageSaveNonce; ?>'>
        <input type='hidden' name='page' value='<?php echo $pageId; ?>'>
        <div class='row'>
            <div class='three columns'>
                <label for='permission'>Permission</label>
                <input class="u-full-width" type="text" name='permission' id='permission' placeholder='(Optional)'
                    value='<?php echo $page['permission']; ?>'>
            </div>
        </div>
        <input type='hidden' name='content' id='page_content'>
    </form>
</div>
<section class="editor">
    <textarea id="editor"><?php echo $page['rawContent']; ?></textarea>
</section>
<div class='row right'>
    <button class="button-primary" onclick='javascript:savePage()'>Save</button>
</div>

<div class='title sub'>
    Page Files
</div>

<div class='page-files'>
    <p><?php echo $fileImgHtml; ?></p>
    <p><?php echo $fileHtml; ?></p>
</div>

<div class='title sub'>
    File Upload
</div>

<form method='post' action='<?php echo $administro->baseDir . 'form/uploadpagefile'; ?>' enctype='multipart/form-data'>
    <input type='hidden' name='nonce' value='<?php echo $pageUploadNonce; ?>'>
    <input type='hidden' name='page' value='<?php echo $pageId; ?>'>
    <input type="file" name="file">
    <input type="submit" value="Upload" name="submit">
</form>

<div class='modal' id='image_modal'>
    <div class='title'>
        Insert Image
    </div>
    <div class='spacer'></div>
    <form onsubmit="return insertImage(this);">
        <div class='row'>
            <div class='six columns'>
                <label for='title'>Image Title</label>
                <input class="u-full-width" type="text" name='title' id='image_title'>
            </div>
            <div class='six columns'>
                <label for='file'>Image File</label>
                <select name='file' id='image_file'>
                    <?php echo $fileImgModal; ?>
                </select>
            </div>
        </div>
        <input type='submit' value='Add Image' name='submit'>
    </form>
</div>

<div class='modal' id='file_modal'>
    <div class='title'>
        Insert file
    </div>
    <div class='spacer'></div>
    <form onsubmit="return insertFile(this);">
        <div class='row'>
            <div class='six columns'>
                <label for='title'>File Title</label>
                <input class="u-full-width" type="text" name='title' id='file_title'>
            </div>
            <div class='six columns'>
                <label for='file'>File</label>
                <select name='file' id='file_file'>
                    <?php echo $fileModal; ?>
                </select>
            </div>
        </div>
        <input type='submit' value='Add File' name='submit'>
    </form>
</div>
<div class='shade' id='shade'></div>

<script>
    var cachedPreview = 'Loading...';
    var cm;
    var simplemde = new SimpleMDE({
        status: ["autosave", "lines", "words"],
        element: document.getElementById("editor"),
        forceSync: true,
        previewRender: function(plainText, preview) {
            var xmlhttp = new XMLHttpRequest();
            var params = "page=<?php echo $pageId; ?>&nonce=<?php echo $pageRenderNonce; ?>&content="+plainText;
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if(this.responseText !== null) {
                        preview.innerHTML = this.responseText;
                        cachedPreview = this.responseText;
                    }
                }
            };
            xmlhttp.open("POST", "<?php echo $administro->baseDir; ?>form/renderpage", true);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(params);
            return cachedPreview;
        },
        toolbar: ["bold", "italic", "heading", "|",
            "quote", "unordered-list", "ordered-list", "|",
            "link", {
                name: "image",
                action: function customFunction(editor){
                    // Get image to insert
                    cm = editor.codemirror;
                    toggleModal('image', true);
                },
                className: "fa fa-picture-o",
                title: "Insert Image",
            },
            {
                name: "file",
                action: function customFunction(editor){
                    // Get image to insert
                    cm = editor.codemirror;
                    toggleModal('file', true);
                },
                className: "fa fa-file-o",
                title: "Insert File",
            },
            "table", "|",
            "preview", "side-by-side", "fullscreen"
        ],
    });

    function toggleModal(id, type) {
        var modal = document.getElementById(id + '_modal');
        var shade = document.getElementById('shade');
        if(type) {
            // Enable the modal
            modal.style.display = 'block';
            shade.style.display = 'block';
            setTimeout(function() {
                modal.style.opacity = 1;
                shade.style.opacity = .3;
            }, 1);
        } else {
            // Disable the modal
            modal.style.opacity = 0;
            shade.style.opacity = 0;
            setTimeout(function() {
                modal.style.display = 'none';
                shade.style.display = 'none';
            }, 250);
        }
    }

    function insertImage(e) {
        var doc = cm.getDoc();
        var cursor = doc.getCursor();
        doc.replaceRange("!["+document.getElementById('image_title').value+"]("+document.getElementById('image_file').value+")", cursor);
        document.getElementById('image_title').value = '';
        toggleModal('image', false);
        return false;
    }

    function insertFile(e) {
        var doc = cm.getDoc();
        var cursor = doc.getCursor();
        doc.replaceRange("["+document.getElementById('file_title').value+"](;"+document.getElementById('file_file').value+")", cursor);
        document.getElementById('file_title').value = '';
        toggleModal('file', false);
        return false;
    }

    function savePage() {
        document.getElementById("page_content").value = document.getElementById("editor").value;
        document.forms["save_page"].submit();
    }
</script>
