<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
use Symfony\Component\Console\Application;
$application = new Application();
$application->run();