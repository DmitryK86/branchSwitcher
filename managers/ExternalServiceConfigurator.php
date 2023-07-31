<?php

declare(strict_types=1);

namespace app\managers;

use app\components\creators\config\ConfigCreatorFactory;
use app\components\creators\config\ServiceConfigCreatorInterface;
use app\models\UserEnvironments;

class ExternalServiceConfigurator
{
    private ConfigCreatorFactory $factory;
    private string $storeConfigPath;

    public function __construct(ConfigCreatorFactory $factory)
    {
        $this->factory = $factory;
        $this->storeConfigPath = \Yii::$app->params['pathToStoreConfigs'];
    }

    public function configure(UserEnvironments $env): void
    {
        if (!$env->related_services_id) {
            return;
        }

        $filename = sprintf("%s/%s.json", rtrim($this->storeConfigPath, '/'), $env->id);
        $configs = [];
        foreach ($env->relatedServices as $serviceEnv) {
            if (!$serviceEnv->project->isServiceProject()) {
                continue;
            }
            if (!$serviceEnv->isReady()) {
                throw new \Exception("Attempt to create config for service in status not ready");
            }

            $configCreatorForEnvType = $env->project->isServiceProject() ? $env->project->code : ServiceConfigCreatorInterface::TYPE_CASINO;
            $confCreators = $this->factory->getCreators($serviceEnv->project->code, $configCreatorForEnvType);
            foreach ($confCreators as $confCreator) {
                $configs = array_merge($configs, $confCreator->create($serviceEnv));
            }
        }

        if (file_put_contents($filename, json_encode($configs)) === false) {
            throw new \Exception("Failed to create config file for env {$env->id}");
        }
    }
}