<?php

    namespace Administro\Config;

    use \Administro\Lib\Spyc;

    class ConfigManager {

        public function getConfiguration() {
            // Create default options
            $defaultValues = array("name" => "My Website", "default-page" => "home");
            // Check if the config directory exists
            if(!file_exists(BASEDIR."config")) {
                mkdir(BASEDIR."config");
            }
            $currentConfig = array();
            // Check if the config file exists
            if(file_exists(BASEDIR."config/config.yaml")) {
                $currentConfig = Spyc::YAMLLoad(BASEDIR."config/config.yaml");
            } else {
                // Generate a new config
                file_put_contents(BASEDIR."config/config.yaml", "");
            }
            // Set configuration values if they don't exist
            foreach($defaultValues as $option => $value) {
                if(!isset($currentConfig[$option])) {
                    $currentConfig[$option] = $value;
                }
            }

            file_put_contents(BASEDIR."config/config.yaml", Spyc::YAMLDump($currentConfig, false, false, true));

            return $currentConfig;
        }

        public function setConfigValue($key, $value) {
            // Load the current config
            $config = self::getConfiguration();
            // Set the new value
            $config[$key] = $value;
            // Save the file
            file_put_contents(BASEDIR."config/config.yaml", Spyc::YAMLDump($config, false, false, true));
        }
    }
