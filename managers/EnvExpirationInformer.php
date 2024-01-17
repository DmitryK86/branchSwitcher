<?php

declare(strict_types=1);

namespace app\managers;

use app\models\User;
use app\models\UserEnvironments;
use app\repository\UserEnvironmentsRepository;
use yii\web\Request;
use yii\web\Cookie;
use yii\web\Response;

class EnvExpirationInformer
{
    private const EXPIRATION_INFO_COOKIE_NAME = 'expiration_info';
    private const EXPIRATION_INFO_COOKIE_TTL = 3600;
    private const ALMOST_EXPIRED_ENV_DAYS = 20;

    private UserEnvironmentsRepository $envRepository;

    public function __construct(UserEnvironmentsRepository $envRepository)
    {
        $this->envRepository = $envRepository;
    }

    public function getExpirationInfo(User $user, Request $request, Response $response): ?array
    {
        if ($request->getCookies()->has(self::EXPIRATION_INFO_COOKIE_NAME)) {
            return null;
        }

        $envs = $this->envRepository->findUserExpiredEnvs($user, self::ALMOST_EXPIRED_ENV_DAYS);
        if (empty($envs)) {
            return null;
        }

        $result = [];
        foreach ($envs as $env) {
            $expirationDate = (new \DateTime($env->updated_at))->modify(sprintf('+%s days', UserEnvironments::EXPIRED_ENV_DAYS));
            $diff = $expirationDate->diff(new \DateTime());
            $removed = ($diff->invert == 0 || $diff->days < 1) ? 'tomorrow' : ("after {$expirationDate->format('d.m')}");
            $result[] = "Env '{$env->environment_code}' is almost expired and will be removed {$removed}";
        }

        $response->getCookies()->add(new Cookie([
            'name' => self::EXPIRATION_INFO_COOKIE_NAME,
            'value' => '1',
            'expire' => time() + self::EXPIRATION_INFO_COOKIE_TTL,
        ]));

        return $result;
    }
}
