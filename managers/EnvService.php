<?php

declare(strict_types=1);

namespace app\managers;

use app\components\resolvers\branch\BranchResolverFactory;
use app\exceptions\BranchResolveException;
use app\exceptions\RepositoryNotFoundException;
use app\models\Repository;
use app\models\User;
use app\models\UserEnvironmentBranches;
use app\models\UserEnvironments;

class EnvService
{
    private CommandBuilder $commandBuilder;
    private BranchResolverFactory $branchResolverFactory;
    private ExternalServiceConfigurator $serviceConfigurator;

    public function __construct(
        CommandBuilder $commandBuilder,
        BranchResolverFactory $branchResolverFactory,
        ExternalServiceConfigurator $serviceConfigurator
    ) {
        $this->commandBuilder = $commandBuilder;
        $this->branchResolverFactory = $branchResolverFactory;
        $this->serviceConfigurator = $serviceConfigurator;
    }

    public function create(UserEnvironments $userEnvironment): void
    {
        if (!$userEnvironment->validate()) {
            throw new \Exception(print_r($userEnvironment->getErrorSummary(true), true));
        }

        if (!\Yii::$app->db->getTransaction()) {
            $t = \Yii::$app->db->beginTransaction();
        }

        try {
            $userEnvironment->save();
            foreach ($userEnvironment->branchesData as $repoCode => $branchName) {
                $repository = Repository::findOne(['code' => $repoCode]);
                if (!$repository) {
                    throw new RepositoryNotFoundException("Repository with code '{$repoCode}' not found");
                }
                $resolver = $this->branchResolverFactory->getByName($repository->version_control_provider);
                if (!in_array($branchName, $resolver->resolve($repository, $branchName))) {
                    throw new BranchResolveException("Branch {$branchName} ({$repository->name}) not found");
                }
                $branch = new UserEnvironmentBranches();
                $branch->user_environment_id = $userEnvironment->id;
                $branch->repository_id = $repository->id;
                $branch->branch = $branchName;
                if (!$branch->save()) {
                    throw new \Exception("Failed to save branch data. Details: " . print_r($branch->getErrorSummary(true), true));
                }
            }

            $this->serviceConfigurator->configure($userEnvironment);

            if (isset($t)) {
                $t->commit();
            }
        } catch (\Throwable $e) {
            if (isset($t)) {
                $t->rollBack();
            }
            throw $e;
        }

        $command = $this->commandBuilder->forCreate($userEnvironment);
        $this->executeCommand($command);
    }

    public function update(UserEnvironments $userEnvironment)
    {
        if (!$userEnvironment->validate()) {
            throw new \Exception(print_r($userEnvironment->getErrorSummary(true), true));
        }

        if (!\Yii::$app->db->getTransaction()) {
            $t = \Yii::$app->db->beginTransaction();
        }

        try {
            $userEnvironment->status = UserEnvironments::STATUS_IN_PROGRESS;
            $userEnvironment->save();
            foreach ($userEnvironment->branchesData as $repoCode => $branchName) {
                $repository = Repository::findOne(['code' => $repoCode]);
                if (!$repository) {
                    throw new RepositoryNotFoundException("Repository with code '{$repoCode}' not found");
                }
                $resolver = $this->branchResolverFactory->getByName($repository->version_control_provider);
                if (!in_array($branchName, $resolver->resolve($repository, $branchName))) {
                    throw new BranchResolveException("Branch {$branchName} ({$repository->name}) not found");
                }
                UserEnvironmentBranches::updateAll(['active' => false],
                    [
                        'user_environment_id' => $userEnvironment->id,
                        'active' => true,
                        'repository_id' => $repository->id
                    ]
                );
                $branch = new UserEnvironmentBranches();
                $branch->user_environment_id = $userEnvironment->id;
                $branch->repository_id = $repository->id;
                $branch->branch = $branchName;
                if (!$branch->save()) {
                    throw new \Exception("Failed to save branch data. Details: " . print_r($branch->getErrorSummary(true), true));
                }
            }

            if (isset($t)) {
                $t->commit();
            }
        } catch (\Throwable $e) {
            if (isset($t)) {
                $t->rollBack();
            }
            throw $e;
        }

        $command = $this->commandBuilder->forUpdate($userEnvironment);
        $this->executeCommand($command);
    }

    public function updateOne(UserEnvironments $userEnvironment, string $repositoryCode, string $branchName)
    {
        $repository = Repository::findOne(['code' => $repositoryCode]);
        if (!$repository) {
            throw new \Exception("Repository with code '{$repositoryCode}' not found");
        }
        if (!in_array($repository->id, $userEnvironment->project->getRepositoriesIdsArray())) {
            throw new \Exception("Repository ID#{$repository->id} doesnt belongs to project {$userEnvironment->project->code}");
        }
        /** @var UserEnvironmentBranches $branchData */
        $branchData = array_filter($userEnvironment->branches, function (UserEnvironmentBranches $branchData) use ($repository) {
            return $branchData->repository_id == $repository->id;
        });
        $branchData = reset($branchData);
        if (!isset($branchData)) {
            throw new \Exception("User env ID#{$userEnvironment->id} doesnt have branch with repository code '{$repository->code}'");
        }

        if (!\Yii::$app->db->getTransaction()) {
            $t = \Yii::$app->db->beginTransaction();
        }

        try {
            $branchData->active = false;
            $branchData->saveOrFail();

            $branch = new UserEnvironmentBranches();
            $branch->user_environment_id = $userEnvironment->id;
            $branch->repository_id = $repository->id;
            $branch->branch = $branchName;
            $branch->saveOrFail();

            $userEnvironment->status = UserEnvironments::STATUS_IN_PROGRESS;
            $userEnvironment->branchesData[$repositoryCode] = $branchName;
            $userEnvironment->saveOrFail();

            if (isset($t)) {
                $t->commit();
            }
        } catch (\Throwable $e) {
            if (isset($t)) {
                $t->rollBack();
            }
            throw $e;
        }

        $command = $this->commandBuilder->forUpdateOne($userEnvironment, $repository, $branchName);
        $this->executeCommand($command);
    }

    public function delete(UserEnvironments $userEnvironment)
    {
        UserEnvironments::removeRelatedServices($userEnvironment);
        $userEnvironment->setInProgress();

        $command = $this->commandBuilder->forDelete($userEnvironment);
        $this->executeCommand($command);
    }

    public function addKey(UserEnvironments $env, array $usersIds)
    {
        /** @var User[] $users */
        $users = User::find()->where(['in', 'id', $usersIds])->all();
        foreach ($users as $user) {
            if (!$user->ssh_key) {
                throw new \Exception("User ID#{$user->username} has no ssh key");
            }

            $this->executeCommand($this->commandBuilder->forAddKey($env, $user));
        }
    }

    public function removeBasicAuth(UserEnvironments $env, int $timeout)
    {
        if ($timeout > UserEnvironments::MAX_REMOVE_AUTH_MINUTES) {
            throw new \Exception("Max timeout is " . UserEnvironments::MAX_REMOVE_AUTH_MINUTES);
        }

        $this->executeCommand($this->commandBuilder->forRemoveAuth($env, $timeout));

        $env->basic_auth_removed_till = (new \DateTime())->modify("+{$timeout} minutes")->format('Y-m-d H:i:00');
        $env->saveOrFail(true, ['basic_auth_removed_till']);
    }

    public function reload(UserEnvironments $env)
    {
        if (!$env->isReady()) {
            throw new \Exception("Can't reload env in not ready status");
        }

        $this->executeCommand($this->commandBuilder->forReload($env));
    }

    public function updateDB(UserEnvironments $env)
    {
        if (!$env->isReady()) {
            throw new \Exception("Can't update env in not ready status");
        }

        $this->executeCommand($this->commandBuilder->forUpdateDB($env));
    }

    private function executeCommand(string $command)
    {
        if (\Yii::$app->user->getIdentity()->isDevops()) {
            $command = str_replace('multistage.sh', 'multistage2.sh', $command);
        }
        $result = shell_exec($command);

        \Yii::warning("\nCommand: {$command}\nResult: {$result}", 'env_creation');
    }
}
