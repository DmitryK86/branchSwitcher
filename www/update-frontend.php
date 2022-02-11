<?php

//$branch = $argv[1] ?? '';
//$stage = $argv[2];
//$project = $argv[1];

$message = shell_exec("ssh dev@157.90.211.146 /var/www/back4/king/update.sh develop");

echo $message;
echo "OK";
