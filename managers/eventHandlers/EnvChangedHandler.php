<?php

declare(strict_types=1);

namespace app\managers\eventHandlers;

use app\components\events\EnvEvent;
use app\managers\AutotestRunner;
use yii\base\Component;

class EnvChangedHandler extends Component
{
    public function onEnvChanged(EnvEvent $event): void
    {
        $env = $event->getEnv();
        if ($env->is_run_autotest) {
            $runner = new AutotestRunner();
            $runner->run($env);
        }
    }
}