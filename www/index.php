<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

if (isset($_REQUEST['action'])){
    $func = $_REQUEST['action'];
    $projectName = $_REQUEST['project-name'] ?? null;
    $branchName = $_REQUEST['branch-name'] ?? null;

    (new Switcher($config['projects']))->$func($projectName, $branchName);
}

(new Switcher())->init();