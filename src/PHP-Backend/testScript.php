<?php
$configPath = require_once(__DIR__ . '\config\config.php');
if (!file_exists($configPath)) {
    die("Config file not found at: $configPath");
}
$config = require_once($configPath);
var_dump($config);
?>