<?php

declare(strict_types=1);

namespace app\components\creators\config;

use app\models\UserEnvironments;

interface ServiceConfigCreatorInterface
{
    public const TYPE_CASINO = 'casino';
    public const TYPE_AMS = 'ams';
    public const TYPE_GAMBLING_PARTNERS = 'gp';

    public function create(UserEnvironments $env): array;

    public static function fromEnvType(): string;
    public static function forEnvType(): string;
}
