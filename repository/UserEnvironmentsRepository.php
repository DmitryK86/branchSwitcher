<?php

declare(strict_types=1);

namespace app\repository;

use app\models\User;
use app\models\UserEnvironments;

class UserEnvironmentsRepository
{
    /**
     * @return UserEnvironments[]
     */
    public function findUserExpiredEnvs(User $user, int $expDaysInterval = 20): array
    {
        return UserEnvironments::find()
            ->where("
            user_id = :userId 
            AND environment_code IS NOT NULL 
            AND status NOT IN ('deleted', 'in_progress') 
            AND is_persist = FALSE 
            AND updated_at <= NOW() - interval '{$expDaysInterval} days'"
            )->params([':userId' => $user->id])
            ->all();
    }

    /**
     * @return UserEnvironments[]
     */
    public function findExpiredEnvs(int $expDaysInterval = 30): array
    {
        return UserEnvironments::find()
            ->where("
            environment_code IS NOT NULL 
            AND status NOT IN ('deleted', 'in_progress') 
            AND updated_at <= NOW() - interval '{$expDaysInterval} days' 
            AND is_persist = FALSE"
            )->all();
    }
}
