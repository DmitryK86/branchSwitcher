<?php

declare(strict_types=1);

namespace app\managers;

use app\models\User;

class UserKeyService
{
    private const KEYS_PATH_CONFIG_NAME = 'rsaKeysPath';

    public function updateSshKey(User $user): void
    {
        if (!$user->ssh_key) {
            return;
        }

        if (!$path = \Yii::$app->params[self::KEYS_PATH_CONFIG_NAME]) {
            throw new \Exception("RSA keys path not defined");
        }

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $filepath = sprintf('%s/%s.pub', rtrim($path, '/'), $user->username);
        if (is_file($filepath)) {
            if (file_get_contents($filepath) === $user->ssh_key) {
                return;
            }
        }

        file_put_contents($filepath, $user->ssh_key);
    }
}