<?php

declare(strict_types=1);

namespace app\helpers;

use app\models\UserEnvironments;

class LogHelper
{
    private const LAST_ROWS_COUNT = 20;

    public static function getLogData(UserEnvironments $env): LogDto
    {
        if (!$logPath = \Yii::$app->params['pathToLogs']) {
            return new LogDto();
        }

        $logFileName = '';
        $files = scandir($logPath, SCANDIR_SORT_DESCENDING) ?: [];
        foreach ($files as $file) {
            if (strpos($file, "{$env->id}.log")) {
                $logFileName = $file;
                break;
            }
        }

        if (!$logFileName) {
            return new LogDto();
        }

        $path = sprintf('%s/%s', rtrim($logPath, '/'), $logFileName);        $content = file_get_contents($path);
        $content = explode("\n", $content);
        $content = array_slice($content, -self::LAST_ROWS_COUNT);
        $content = implode("<br>", $content);

        return new LogDto($logFileName, $content);
    }
}
