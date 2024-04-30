<?php

declare(strict_types=1);

namespace app\managers;

use app\models\Repository;
use app\models\User;
use app\models\UserEnvironments;
use yii\helpers\ArrayHelper;

class CommandBuilder
{
    private const ACTION_CREATE = 'create';
    private const ACTION_UPDATE = 'update';
    private const ACTION_UPDATE_ONE = 'updatebranch';
    private const ACTION_DELETE = 'delete';
    private const ACTION_ADD_KEY = 'addssh';
    private const ACTION_REMOVE_AUTH = 'remove_auth';
    private const ACTION_RELOAD = 'reload';
    private const ACTION_UPDATE_DB = 'updateDB';

    private const ACTION = 'ACTION';
    private const COMMAND_MACROS_DATE = '{DATE}';
    private const COMMAND_MACROS_ENV_ID = '{ENV_ID}';
    private const COMMAND_MACROS_PROJECT = '{PROJECT}';
    private const COMMAND_MACROS_USER = '{SSH_USER}';
    private const COMMAND_MACROS_ENV_CODE = '{HASH_NAME}';
    private const COMMAND_MACROS_PROJECT_PARAMS = '{PROJECT_PARAMS}';
    private const COMMAND_MACROS_UPDATE_ONE_BRANCH = '{ONE_BRANCH_DATA}';

    public function forCreate(UserEnvironments $env): string
    {
        $params = [
            self::COMMAND_MACROS_USER => $env->user->username,
            self::ACTION => self::ACTION_CREATE,
            self::COMMAND_MACROS_PROJECT_PARAMS => $env->project->params,
        ];

        //if ($env->user->env_params) {
        //    $params[] = "params={$env->user->env_params}";
        //}

        return $this->create($this->addBranches($env, $params), $env);
    }

    public function forUpdate(UserEnvironments $env): string
    {
        $params = [
            self::ACTION => self::ACTION_UPDATE,
        ];

        return $this->create($this->addBranches($env, $params), $env);
    }

    public function forUpdateOne(UserEnvironments $env, Repository $repository, string $branchName): string
    {
        // TODO fix it with devops
        switch ($repository->name) {
            case 'back':
                $repoCode = 'back';
                break;
            case 'front_nuxt':
                $repoCode = 'secondfront';
                break;
            case 'front':
            case 'front_usa':
                $repoCode = 'front';
                break;
            default:
                throw new \Exception("Unknown repository name {$repository->name}");
        }

        $params = [
            self::ACTION => self::ACTION_UPDATE_ONE,
            self::COMMAND_MACROS_UPDATE_ONE_BRANCH => sprintf("%s '%s'", $repoCode, $branchName),
        ];

        return $this->create($params, $env);
    }

    public function forDelete(UserEnvironments $env): string
    {
        $params = [
            self::ACTION => self::ACTION_DELETE,
        ];

        return $this->create($params, $env);
    }

    public function forAddKey(UserEnvironments $env, User $user): string
    {
        $params = [
            self::COMMAND_MACROS_USER => $user->username,
            self::ACTION => self::ACTION_ADD_KEY,
        ];

        return $this->create($params, $env);
    }

    public function forRemoveAuth(UserEnvironments $env, int $timeout): string
    {
        $params = [
            '{MINUTES}' => $timeout,
            self::ACTION => self::ACTION_REMOVE_AUTH,
        ];

        return $this->create($params, $env);
    }

    public function forReload(UserEnvironments $env): string
    {
        $params = [
            self::ACTION => self::ACTION_RELOAD,
        ];

        return $this->create($params, $env);
    }

    public function forUpdateDB(UserEnvironments $env): string
    {
        $params = [
            self::ACTION => self::ACTION_UPDATE_DB,
        ];

        return $this->create($params, $env);
    }

    private function addBranches(UserEnvironments $env, array $params): array
    {
        $result = [];

        foreach (Repository::find()->all() as $repository) {
            $result[$repository->code] = '';
        }
        foreach ($env->branches as $branch) {
            $result[$branch->repository->code] = $branch->branch;
        }

        // TODO make it better
        if (in_array($env->project_id, [1,7])) {
            $result['FRONT_NUXT_BRANCH'] = 'none';
        }

        return ArrayHelper::merge($params, $result);
    }

    private function create(array $params, UserEnvironments $env): string
    {
        $params = array_merge($params, $this->getRequiredParams($env));
        $template = $env->project->getCustomCommandTemplate($params[self::ACTION])
                ?: \Yii::$app->params['commands'][$params[self::ACTION]];

        $result = str_replace(
            array_keys($params),
            array_values($params),
            $template
        );

        return str_replace("''", '', $result);
    }

    private function getRequiredParams(UserEnvironments $env): array
    {
        return [
            self::COMMAND_MACROS_ENV_ID => $env->id,
            self::COMMAND_MACROS_ENV_CODE => $env->environment_code,
            self::COMMAND_MACROS_PROJECT => $env->project->code,
            self::COMMAND_MACROS_DATE => date('Y-m-d_H-i-s'),
        ];
    }

    public static function getActions(): array
    {
        $result = [];
        $obj = new \ReflectionClass(__CLASS__);
        foreach ($obj->getConstants() as $key => $val) {
            if (strpos($key, 'ACTION_') !== 0) {
                continue;
            }

            $name = str_replace('ACTION_', '', $val);
            $name = str_replace('_', ' ', $name);
            $result[$val] = ucfirst(strtolower($name));
        }

        return $result;
    }
}