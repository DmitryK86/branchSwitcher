<?php

namespace app\commands;

use app\managers\EnvService;
use app\models\UserEnvironments;
use app\repository\UserEnvironmentsRepository;
use yii\console\Controller;

class EnvController extends Controller
{
    private EnvService $envService;
    private UserEnvironmentsRepository $envRepository;

    public function __construct($id, $module, EnvService $envService, UserEnvironmentsRepository $envRepository)
    {
        parent::__construct($id, $module);
        $this->envService = $envService;
        $this->envRepository = $envRepository;
    }

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

    public function actionRemoveExpiredEnvs()
    {
        $expiredEnvs = $this->envRepository->findExpiredEnvs(UserEnvironments::EXPIRED_ENV_DAYS);
        foreach ($expiredEnvs as $expiredEnv) {
            $expDate = $expiredEnv->updated_at;
            $this->envService->delete($expiredEnv);
            \Yii::warning("Env ID#{$expiredEnv->id} was deleted (expire: {$expDate})", 'env.remove.command');
        }
    }
}