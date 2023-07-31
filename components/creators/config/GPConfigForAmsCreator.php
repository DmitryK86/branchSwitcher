<?php

namespace app\components\creators\config;

use app\helpers\EnvUrlBuilder;
use app\models\UserEnvironments;

class GPConfigForAmsCreator implements ServiceConfigCreatorInterface
{
    public function create(UserEnvironments $gpEnv): array
    {
        return [
            'GP_CALLBACK_URL' => EnvUrlBuilder::build($gpEnv, EnvUrlBuilder::TYPE_ADMIN),
        ];
    }

    public static function fromEnvType(): string
    {
        return self::TYPE_GAMBLING_PARTNERS;
    }

    public static function forEnvType(): string
    {
        return self::TYPE_AMS;
    }
}
