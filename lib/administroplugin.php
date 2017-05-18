<?php

abstract class AdministroPlugin {

    var $administro;

    public function __construct($administro) {
        $this->administro = $administro;
    }

    public function callEvent($event) {
        // Check if the method exists
        if(method_exists($this, $event)) {
            // Call the event
            call_user_func(array($this, $event));
        }
    }

}
