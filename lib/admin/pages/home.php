<?php
    use Symfony\Component\Yaml\Yaml;

    // Load config
    $config = $administro->config;
    // Generate form nonce
    $configNonce = $administro->generateNonce('adminconfig');
    // Check if update is available
    $updateAvailable = false;
    $latest = '';
    if(file_exists($administro->rootDir . 'latest_update.yaml')) {
        $latest = Yaml::parse(file_get_contents($administro->rootDir . 'latest_update.yaml'))['version'];
        if(version_compare($latest, $config['version']) > 0) {
            // Update available
            $updateAvailable = true;
        }
    }
?>
<div class='title sub'>
    Administro Status
</div>
<div>
    <div><b>Current Version:</b> <?php echo $config['version']; ?></div>
    <?php if(!empty($latest)){echo '<div><b>Latest Version:</b> ' . $latest . '</div>';}; ?>
    <div class='spacer'></div>
    <?php
        if($updateAvailable) {
            echo '<form action="' . $administro->baseDir . 'form/update" method="post">
                <input type="hidden" name="nonce" value="' . $administro->generateNonce('update') . '">
                <input class="button-primary" type="submit" value="Update Now">
            </form>';
        } else {
            echo '<form action="' . $administro->baseDir . 'form/updatecheck" method="post">
                <input type="hidden" name="nonce" value="' . $administro->generateNonce('updatecheck') . '">
                <input class="button-primary" type="submit" value="Check for Update">
            </form>';
        }
    ?>
</div>
<div class='title sub'>
    Configuration
</div>
<form action='<?php echo $administro->baseDir . 'form/adminconfig' ?>' method='post'>
    <div class='row'>
        <div class='three columns'>
            <label for='title'>Site Title</label>
            <input class="u-full-width" type="text" name='title' id='title' value='<?php echo $config['title']; ?>'>
        </div>
    </div>
    <input type='hidden' name='nonce' value='<?php echo $configNonce; ?>'>
    <input class="button-primary" type="submit" value="Save">
</form>
