<?php

namespace app\managers;

use yii\log\Logger as BaseLogger;

class Logger
{
    public function log($message)
    {
        \Yii::getLogger()->log($message, BaseLogger::LEVEL_ERROR);
    }
}
