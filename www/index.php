<?php

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

$adminId = 'back4';//$_REQUEST['admin-id'] ?? null;
$switcher = new Switcher();
try {
    $switcher->init($adminId);
    $func = $_REQUEST['action'];
    $projectName = $_REQUEST['project-name'] ?? null;
    $branchName = $_REQUEST['branch-name'] ?? null;

    $switcher->$func($projectName, $branchName);
} catch (Exception $e){
    (new Logger())->log($e->getMessage());
    $switcher->sendResponse(Switcher::STATUS_ERROR, $e->getMessage());
}

function trace($data, $terminate = true)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    if ($terminate){
        exit(0);
    }
}

