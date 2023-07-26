<?php

declare(strict_types=1);

namespace app\components\creators\config;

use app\models\UserEnvironments;

interface ServiceConfigCreatorInterface
{
    public function create(UserEnvironments $env): array;

    public static function getType(): string;
}
