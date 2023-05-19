<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAssetRefreshBranches extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/refreshBranches.js?v=1',
    ];
    public $css = [
        'css/refreshBranches.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}