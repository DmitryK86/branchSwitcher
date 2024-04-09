<?php

declare(strict_types=1);

namespace app\managers\eventHandlers;

use app\components\events\EnvCreatedEvent;
use app\managers\AutotestRunner;
use yii\base\Component;

class EnvCreatedHandler extends Component
{
    public function onEnvCreated(EnvCreatedEvent $event): void
    {
        $env = $event->getEnv();
        if ($env->is_run_autotest) {
            $runner = new AutotestRunner();
            $runner->run($env);
        }
    }
}