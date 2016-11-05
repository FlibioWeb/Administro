<?php

    namespace Administro\Admin;

    abstract class AdminRoute {

        // Gets if the route is visible in the sidebar.
        abstract function isVisible();

        // Gets the route icon using Font Awesome.
        abstract function getIcon();

        // Gets the route name.
        abstract function getName();

        // Gets the name of the admin partial.
        abstract function getPartial();

        // Check if the params make a valid route.
        abstract function isValid($params);

    }
