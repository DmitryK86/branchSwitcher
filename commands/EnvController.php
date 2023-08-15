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

    public function actionRemoveExpiredBasicAuthTimeouts()
    {
        $envs = UserEnvironments::find()->where('basic_auth_removed_till <= now()')->all();
        if (!$envs) {
            return;
        }

        foreach ($envs as $env) {
            $env->basic_auth_removed_till = null;
            $env->saveOrFail(false, ['basic_auth_removed_till']);
        }
    }
}