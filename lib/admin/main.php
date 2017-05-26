<?php

    $administro = $GLOBALS['administro'];
    // Rewrite home route
    $mainParams = $administro->params;
    if(count($mainParams) === 1) {
        $administro->redirect('admin/home');
    }

    // Verify page exists
    $currentAdminPageId = $mainParams[1];
    if(!isset($administro->adminPages[$currentAdminPageId])) {
        // Page not found
        $administro->redirect('admin/home');
    }

    // Load message
    $message = $administro->variables['message'];
    $messageType = ' ' . $administro->variables['message_type'] . (empty($administro->variables['message_type']) ? '' : ' active');
?>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin | <?php echo $administro->config['title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Raleway:300,400,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://opensource.keycdn.com/fontawesome/4.7.0/font-awesome.min.css">
    <!-- Style -->
    <link rel="stylesheet" href="<?php echo $administro->baseDir . 'assets/admin/main.css'; ?>">
</head>

<body>

    <div class='sidebar'>
        <div class='header'>
            <a href='<?php echo $administro->baseDir ?>'>Administro</a>
        </div>
        <div class='navigation'>
            <?php
                foreach($administro->adminPages as $pageId => $pageData) {
                    if(isset($pageData['hide']) && $pageData['hide']) continue;
                    $class = '';
                    if($pageId == $currentAdminPageId) {
                        $class = 'class="selected"';
                    }
                    echo '<div ' . $class . '><a href="' . $administro->baseDir . 'admin/' . $pageId . '">' .
                    '<i class="fa fa-' . $pageData['icon'] . '"></i> ' . $pageData['name'] . '</a></div>';
                }
            ?>
        </div>
    </div>

    <div class='main'>
        <?php require_once $administro->rootDir . '/' . $administro->adminPages[$currentAdminPageId]['file']; ?>
    </div>

    <div class='message<?php echo $messageType; ?>' id='message'>
        <?php echo $message; ?>
    </div>

</body>

</html>
