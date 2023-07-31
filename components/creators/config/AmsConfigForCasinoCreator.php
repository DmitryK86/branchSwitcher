<?php

namespace app\components\creators\config;

use app\models\UserEnvironments;

class AmsConfigForCasinoCreator implements ServiceConfigCreatorInterface
{
    public function create(UserEnvironments $amsEnv): array
    {
        return [
            'APP_AMS_RABBITMQ_HOST' => $amsEnv->ip,
        ];
    }

    public static function fromEnvType(): string
    {
        return self::TYPE_AMS;
    }

    public static function forEnvType(): string
    {
        return self::TYPE_CASINO;
    }
}
