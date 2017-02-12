<?php

    namespace Administro\Config;

    use \Administro\Lib\Spyc;

    class ConfigManager {

        private $defaultValues;

        public function getConfiguration() {
            // Create default options
            $this->defaultValues = array("name" => "My Website", "default-page" => "home", "data" => array("images/myfile.jpg" => "none"));
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
            foreach($this->defaultValues as $option => $value) {
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

        public function defaultValue($key, $value) {
            $this->defaultValues[$key] = $value;
        }
    }
