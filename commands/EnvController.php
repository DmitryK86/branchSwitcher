<?php

namespace app\commands;

use app\models\UserEnvironments;
use yii\console\Controller;

class EnvController extends Controller
{
    public function actionRemoveDeletedEnvs(string $deletedTillMinutesAgo = '20')
    {
        $deleteTillDate = date(DATE_RFC3339, strtotime("-{$deletedTillMinutesAgo} minutes"));

        $count = UserEnvironments::deleteAll(
            'status = :status AND updated_at <= :deleteTillDate',
            [':status' => UserEnvironments::STATUS_DELETED, ':deleteTillDate' => $deleteTillDate]
        );

        echo "Deleted {$count} envs" . PHP_EOL;
    }
}