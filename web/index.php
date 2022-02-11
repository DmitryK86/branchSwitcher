<?php

$localEnvPath = __DIR__ . '/../config/local/env.php';
if (file_exists($localEnvPath)){
    require $localEnvPath;
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
$localConfigPath = __DIR__ . '/../config/local/web.php';
if (file_exists($localConfigPath)){
    $configLocal = require $localConfigPath;
    $config = \yii\helpers\ArrayHelper::merge($config, $configLocal);
}

(new yii\web\Application($config))->run();

function trace($data, $terminate = false){
    echo '<pre>';
    \yii\helpers\VarDumper::dump($data);
    echo '</pre>';
    if ($terminate){
        echo '<h2>Program terminated</h2>';
        Yii::$app->end();
    }
}
