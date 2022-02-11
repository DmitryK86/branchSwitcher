<?php

function removeDirectory($dir)
{
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        if( is_dir("$dir/$file") ) {
            removeDirectory("$dir/$file");
        } else {
            unlink("$dir/$file");
        }
    }
    return rmdir($dir);
}

function createDirectory($dst,$mode=null,$recursive=false)
{
    if($mode===null)
        $mode=0777;
    $prevDir=dirname($dst);
    if($recursive && !is_dir($dst) && !is_dir($prevDir)) {
        createDirectory(dirname($dst), $mode, true);
    }
    $res=mkdir($dst, $mode);
    @chmod($dst,$mode);
    return $res;
}

$appRootDir = __DIR__ ;

// Re-Creating runtime dirs
$runtimeDirs = [
    "runtime",
    "web/assets",
];

foreach ($runtimeDirs as $runtimeDir ) {
    $dir = $appRootDir . "/" . $runtimeDir;
    if( file_exists($dir) ) {
        removeDirectory($dir);
    }
    echo "creating runtime directory: $dir\n";
    createDirectory($dir, 0777,true);
}

// Creating missing resource directories
$resourceDirs = [
    "web/uploads",
];
foreach ($resourceDirs as $resourceDir ) {
    $dir = $appRootDir . "/" . $resourceDir;
    if( !file_exists($dir)) {
        echo "creating resource directory: $dir\n";
        createDirectory($dir, 0777, true);
    }
}

// Creating missing configs
$exampleConfigFiles = glob($appRootDir . "/config/*-example.php");
foreach( $exampleConfigFiles as $exampleConfigFile ) {
    $configFile = $appRootDir . "/config/"
        . str_replace("-example","",basename($exampleConfigFile));
    if( !file_exists($configFile)) {
        echo "creating new config file from example: " . basename($configFile) . "\n";
        copy($exampleConfigFile,$configFile);
    }
}

echo "Folders initialization complete!\n";

