<?php

declare(strict_types=1);

namespace app\managers;

use app\models\Repository;
use app\models\UserEnvironments;
use yii\helpers\ArrayHelper;

class CommandBuilder
{
    private const CREATE_COMMAND = 'create';
    private const UPDATE_COMMAND = 'update';
    private const UPDATE_ONE_COMMAND = 'updatebranch';
    private const DELETE_COMMAND = 'delete';

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
            self::COMMAND_MACROS_ENV_ID => $env->id,
            self::COMMAND_MACROS_PROJECT => $env->project->code,
            self::COMMAND_MACROS_USER => $env->user->username,
            self::COMMAND_MACROS_DATE => date('Y-m-d_H-i-s'),
            self::ACTION => self::CREATE_COMMAND,
            self::COMMAND_MACROS_PROJECT_PARAMS => $env->project->params,
        ];

        //if ($env->user->env_params) {
        //    $params[] = "params={$env->user->env_params}";
        //}

        return $this->create($this->addBranches($env, $params));
    }

    public function forUpdate(UserEnvironments $env): string
    {
        $params = [
            self::COMMAND_MACROS_ENV_ID => $env->id,
            self::COMMAND_MACROS_ENV_CODE => $env->environment_code,
            self::COMMAND_MACROS_PROJECT => $env->project->code,
            self::COMMAND_MACROS_DATE => date('Y-m-d_H-i-s'),
            self::ACTION => self::UPDATE_COMMAND,
        ];

        return $this->create($this->addBranches($env, $params));
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
            self::COMMAND_MACROS_ENV_ID => $env->id,
            self::COMMAND_MACROS_ENV_CODE => $env->environment_code,
            self::COMMAND_MACROS_DATE => date('Y-m-d_H-i-s'),
            self::ACTION => self::UPDATE_ONE_COMMAND,
            self::COMMAND_MACROS_UPDATE_ONE_BRANCH => sprintf("%s '%s'", $repoCode, $branchName),
        ];

        return $this->create($params);
    }

    public function forDelete(UserEnvironments $env): string
    {
        $params = [
            self::COMMAND_MACROS_ENV_ID => $env->id,
            self::COMMAND_MACROS_ENV_CODE => $env->environment_code,
            self::COMMAND_MACROS_DATE => date('Y-m-d_H-i-s'),
            self::ACTION => self::DELETE_COMMAND,
        ];

        return $this->create($params);
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
        if ($env->project_id == 1) {
            $result['FRONT_NUXT_BRANCH'] = 'none';
        }

        return ArrayHelper::merge($params, $result);
    }

    private function create(array $params): string
    {
        $result = str_replace(
            array_keys($params),
            array_values($params),
            \Yii::$app->params['commands'][$params[self::ACTION]]
        );

        return str_replace("''", '', $result);
    }
}