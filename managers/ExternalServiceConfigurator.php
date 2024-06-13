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
        $configs = [];
        $chosenServicesNames = [];
        if ($env->related_services_id) {
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
                $chosenServicesNames[] = $serviceEnv->project->code;
            }
        }

        if ($env->foreign_related_services_id) {
            // TODO вынести в креаторы
            foreach ($env->foreignRelatedServices as $foreignServiceEnv) {
                if (in_array($foreignServiceEnv->params['type'], $chosenServicesNames)) {
                    continue;
                }
                if ($foreignServiceEnv->params['type'] == 'ams') {
                    $domainPieces = explode('.', $foreignServiceEnv->params['domain']);
                    $domainPieces[0] = 'rabbitmq';
                    $amsBusDomain = implode('.', $domainPieces);
                    $configs = array_merge($configs, ['APP_AMS_RABBITMQ_HOST' => $amsBusDomain]);
                }
            }
        }

        if (!empty($configs)) {
            $filename = sprintf("%s/%s.json", rtrim($this->storeConfigPath, '/'), $env->id);
            if (file_put_contents($filename, json_encode($configs)) === false) {
                throw new \Exception("Failed to create config file for env {$env->id}");
            }
        }
    }
}