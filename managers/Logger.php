<?php

namespace app\managers;

use app\dto\SwitchLogDto;
use app\models\SwitchLog;
use yii\log\Logger as BaseLogger;

class Logger
{
    public function log($message)
    {
        \Yii::getLogger()->log($message, BaseLogger::LEVEL_ERROR);
    }

    public function logSwitch(SwitchLogDto $dto)
    {
        $log = new SwitchLog();
        $log->user_id = $dto->user->id;
        $log->from_branch = $dto->from;
        $log->to_branch = $dto->to;
        $log->alias = $dto->alias;
        $log->project = $dto->project;
        $log->status = $dto->status;

        if (!$log->save()) {
            \Yii::getLogger()->log(json_encode($log->getErrors(), JSON_PRETTY_PRINT), BaseLogger::LEVEL_ERROR);
        }
    }
}
