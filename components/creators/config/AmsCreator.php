<?php

namespace app\components\creators\config;

use app\models\UserEnvironments;

class AmsCreator implements ServiceConfigCreatorInterface
{
    public const TYPE = 'ams';

    public function create(UserEnvironments $amsEnv): array
    {
        return [
            'APP_AMS_RABBITMQ_HOST' => $amsEnv->ip,
        ];
    }

    public static function getType(): string
    {
        return self::TYPE;
    }
}
