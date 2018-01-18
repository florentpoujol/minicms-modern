<?php

require __dir__ . "/../vendor/autoload.php";
require __dir__ . "/../../standard-components/vendor/autoload.php"; // todo: remove when the php-standard-component is finally included as a composer dependency here

require "BaseTestCase.php";
require "DatabaseTestCase.php";

session_start();
