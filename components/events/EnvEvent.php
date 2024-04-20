<?php

declare(strict_types=1);

namespace app\components\events;

use app\models\UserEnvironments;
use yii\base\Event;

abstract class EnvEvent extends Event
{
    private UserEnvironments $env;

    public function __construct(UserEnvironments $env, $config = [])
    {
        $this->env = $env;
        parent::__construct($config);
    }

    public function getEnv(): UserEnvironments
    {
        return $this->env;
    }
}
