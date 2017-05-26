<?php

use Symfony\Component\Yaml\Yaml;

abstract class AdministroPlugin {

    var $administro, $pluginName;

    public function __construct($administro, $pluginName) {
        $this->administro = $administro;
        $this->pluginName = $pluginName;
    }

    public function callEvent($event) {
        // Check if the method exists
        if(method_exists($this, $event)) {
            // Call the event
            call_user_func(array($this, $event));
        }
    }

    public function getInfo() {
        return Yaml::parse(file_get_contents($this->administro->rootDir . 'plugins/' . $this->pluginName . '/plugin.yaml'));
    }

    public function setInfo($info) {
        file_put_contents($this->administro->rootDir . 'plugins/' . $this->pluginName . '/plugin.yaml', Yaml::dump($info));
    }

    public function hasOldInfo() {
        $info = $this->getInfo();
        if(isset($info['latest'])) {
            $elapsed = (new DateTime())->getTimestamp() - (new DateTime($info['latest']['time']))->getTimestamp();
            if($elapsed < 3600) {
                return false;
            }
        }
        return true;
    }

    public function getLatest() {
        $info = $this->getInfo();
        $context = stream_context_create(array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT'])));
        $latest = Yaml::parse(file_get_contents('https://raw.githubusercontent.com/' . $info['repository'] . '/master/plugin.yaml',
            false, $context));
        // Write latest
        $info['latest']['version'] = $latest['version'];
        $info['latest']['time'] = (new DateTime())->format('Y-m-d H:i');
        $this->setInfo($info);
        return $info;
    }

}
