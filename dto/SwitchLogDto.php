<?php

declare(strict_types=1);

namespace app\dto;

use app\models\SwitchLog;
use app\models\User;

class SwitchLogDto
{
    public User $user;
    public string $alias;
    public string $project;
    public string $from;
    public string $to;
    public string $status = SwitchLog::STATUS_SUCCESS;
}
