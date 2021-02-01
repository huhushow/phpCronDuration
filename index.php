<?php
require_once "vendor/autoload.php";
$instance = new Huhushow\CronDuration\CronDuration("* * * * * *");
// $instance = new DateTimeZone('Etc/UCT');
echo json_encode($instance->test());
echo get_class($instance);
