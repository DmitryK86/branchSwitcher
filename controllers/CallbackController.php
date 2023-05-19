<?php

declare(strict_types=1);

namespace app\controllers;

use app\exceptions\EnvironmentNotFoundException;
use app\exceptions\IllegalEnvStateException;
use app\models\UserEnvironments;
use yii\web\Controller;

class CallbackController extends Controller
{
    public function actionIndex(int $envId, string $code, string $status)
    {
        $env = UserEnvironments::findOne(['id' => $envId]);
        if (!$env) {
            throw new EnvironmentNotFoundException("Env '{$envId}' not found", 404);
        }
        if (!$env->isInProgress()) {
            throw new IllegalEnvStateException("Env #{$env->id} not in progress", 409);
        }

        $env->status = $status;
        $env->environment_code = $code;

        if (!$env->save(true, ['status', 'environment_code', 'updated_at'])) {
            throw new \Exception("Env saving error. Details: " . print_r($env->getErrorSummary(true), true));
        }
    }
}
