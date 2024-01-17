<?php

declare(strict_types=1);

namespace app\managers;

use app\models\User;

class MaxEnvCountResolver
{
    private const MAX_ENVS_PER_PROJECT = 1;
    private const MAX_ENVS_PROP_NAME = 'max_envs';

    public function resolveForUserAndProject(User $user, int $projectId): int
    {
        return $this->fromJson($user->env_params, $projectId) ?: $this->fromJson(
            $user->group->params,
            $projectId
        ) ?: self::MAX_ENVS_PER_PROJECT;
    }

    private function fromJson(string $jsonParams, int $projectId): ?int
    {
        $maxEnvs = json_decode($jsonParams, true)[self::MAX_ENVS_PROP_NAME] ?? null;
        if (!$maxEnvs) {
            return null;
        }
        if (is_array($maxEnvs)) {
            return isset($maxEnvs[$projectId]) ? (int)$maxEnvs[$projectId] : null;
        }

        return (int)$maxEnvs;
    }
}
