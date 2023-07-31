<?php

declare(strict_types=1);

namespace app\helpers;

use app\models\UserEnvironments;

class EnvUrlBuilder
{
    public const TYPE_WEB = 'WEB';
    public const TYPE_ADMIN = 'Admin';
    public const TYPE_CALLBACK = 'Callback';
    public const TYPE_RABBIT = 'RabbitMQ';

    public const AVAILABLE_TYPES = [
        self::TYPE_WEB,
        self::TYPE_ADMIN,
        self::TYPE_CALLBACK,
        self::TYPE_RABBIT,
    ];

    public static function build(UserEnvironments $env, string $type)
    {
        if (!in_array($type, self::AVAILABLE_TYPES)) {
            throw new \Exception("Unknown link type '{$type}'");
        }

        $domain = \Yii::$app->params['stageDomain'];
        $prefix = \Yii::$app->params['stageSubdomainPrefixes'][$env->project->type][$type];
        $code = $env->environment_code;

        return "https://{$code}{$prefix}.{$domain}";
    }
}