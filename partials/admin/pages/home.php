<?php
    use \Administro\Form\FormUtils;

    $updater = $administro->updater;

    // Set form tokens
    $updateToken = FormUtils::generateToken("update");
    $clearcacheToken = FormUtils::generateToken("clearcache");

    // Administro Version
    $version = "N/A";
    $currentVersion = $updater->getCurrentVersion();
    if($currentVersion !== false) {
        $version = $currentVersion;
    }

    // Update Check
    $updateAvailable = $updater->checkForUpdate();
?>
<section class="maintenance">
    <header class="title">
        Maintenance
    </header>
    <p>Current Version: <?php echo $version; ?></p>
    <section class="bottom">
        <form class="form update" method="POST" action="<?php echo BASEPATH; ?>form/update">
            <input type="hidden" name="token" value="<?php echo $updateToken; ?>">
            <button type="submit" <?php if(!$updateAvailable) echo "style='display: none;'"; ?>><i class="fa fa-cloud-download"></i> Update</button>
        </form>
        <form class="form cache" method="POST" action="<?php echo BASEPATH; ?>form/clearcache">
            <input type="hidden" name="token" value="<?php echo $clearcacheToken; ?>">
            <button type="submit"><i class="fa fa-refresh"></i> Clear Cache</button>
        </form>
    </section>
</section>
