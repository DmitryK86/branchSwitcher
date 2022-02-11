<?php

$branch = $argv[1];
$stage = $argv[2];
$project = $argv[3];

if (!$branch || !$stage || !$project){
    throw new Exception('Params missing');
}

echo shell_exec("sudo -u dmitry /home/dmitry/update_backend.sh {$branch} {$stage} {$project}");
