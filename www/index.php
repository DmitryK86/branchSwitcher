<?php

require ('Switcher.php');

if (isset($_REQUEST['action'])){
    $func = $_REQUEST['action'];
    $projectName = $_REQUEST['project-name'] ?? null;
    $branchName = $_REQUEST['branch-name'] ?? null;

    (new Switcher())->$func($projectName, $branchName);
}
