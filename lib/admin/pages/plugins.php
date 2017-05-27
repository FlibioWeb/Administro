<div class='title'>
    Plugins
</div>
<div class='spacer'></div>
<?php
    $updateNonce = $administro->generateNonce('updateplugin');
    foreach($administro->plugins as $id => $plugin) {
        $info;
        if($plugin->hasOldInfo()) {
            // Get latest info
            $info = $plugin->getLatest();
        } else {
            // Use current info
            $info = $plugin->getInfo();
        }
        // Check if an update is available
        $update = '';
        if(version_compare($info['latest']['version'], $info['version']) > 0) {
            $update = '<form action="' . $administro->baseDir . 'form/updateplugin" method="post">
                <input type="hidden" name="nonce" value="' . $updateNonce . '">
                <input type="hidden" name="plugin" value="' . $id . '">
                <input class="button-secondary" type="submit" value="Update Now">
            </form>';
        }
        echo '<div class="plugin"><div><b>' . $id . '</b></div><div>Version: ' . $info['version'] . '</div>' . $update . '</div>';
    }
?>
