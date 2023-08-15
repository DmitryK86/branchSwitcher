<?php

namespace app\controllers;

use app\components\resolvers\branch\BranchResolverFactory;
use app\managers\EnvService;
use app\models\Project;
use app\models\Repository;
use Yii;
use app\models\UserEnvironments;
use app\models\forms\UserEnvironmentsSearchForm;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\log\Logger;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class EnvironmentsController extends Controller
{
    private EnvService $envService;

    public function __construct($id, $module, $config = [], EnvService $envService)
    {
        parent::__construct($id, $module, $config);

        $this->envService = $envService;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new UserEnvironmentsSearchForm(Yii::$app->getUser()->getIdentity());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new UserEnvironments();

        if ($model->load(Yii::$app->request->post())) {
            $model->user_id = Yii::$app->getUser()->getId();

            try {
                $this->envService->create($model);
                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $model->project_id = null;
                $model->addError('project_id', $e->getMessage());
                Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
            }
        }

        $query = (new \yii\db\Query())->from('project')->where('enabled = TRUE');
        if ($projects = \Yii::$app->user->getIdentity()->getProjects()) {
            $query->andWhere('id IN (' . implode(',', $projects) .')');
        }
        return $this->render('create', [
            'model' => $model,
            'availableProjects' => $query->all(),
        ]);
    }

    public function actionBranches(string $code, string $searchBranch): Response
    {
        $repository = Repository::findOne(['code' => $code]);
        if (!$repository) {
            return $this->asJson(['success' => false, 'message' => "Repository with code '{$code}' not found"]);
        }

        $resolver = (new BranchResolverFactory())->getByName($repository->version_control_provider);
        $branches = $resolver->resolve($repository, $searchBranch);

        return $this->asJson(['success' => true, 'branches' => $branches]);
    }

    public function actionRepositories(int $projectId): Response
    {
        $project = Project::findOne(['id' => $projectId]);
        if (!$project) {
            return $this->asJson(['success' => false, 'message' => "Project ID#{$projectId} not found"]);
        }
        $result = [];
        foreach ($project->repositories as $repository) {
            if (!$repository->enabled) {
                continue;
            }
            $result[$repository->code] = [
                'defaultBranch' => $repository->default_branch_name,
                'provider' => $repository->version_control_provider,
            ];
        }

        return $this->asJson(['success' => true, 'repositoriesData' => $result]);
    }

    public function actionDelete(int $id): Response
    {
        $env = $this->findModel($id);
        $this->envService->delete($env);

        return $this->redirect(['view', 'id' => $env->id]);
    }

    public function actionUpdate(int $id): Response
    {
        $env = $this->findModel($id);
        if ($env->load(Yii::$app->request->post())) {
            try {
                $this->envService->update($env);
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                Yii::getLogger()->log($e, Logger::LEVEL_ERROR);
            }
        }

        return $this->redirect(['view', 'id' => $env->id]);
    }

    public function actionUpdateOne(int $id, string $repositoryCode, string $branchName): Response
    {
        $env = $this->findModel($id);
        try {
            $this->envService->updateOne($env, $repositoryCode, $branchName);
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::getLogger()->log($e, Logger::LEVEL_ERROR);
        }

        return $this->redirect(['view', 'id' => $env->id]);
    }

    public function actionUpdateComment(int $id): Response
    {
        $env = $this->findModel($id);
        if ($comment = Yii::$app->request->getBodyParam('comment')) {
            $env->comment = $comment;
            if (!$env->save()) {
                throw new \Exception(json_encode($env->getErrorSummary(true)));
            }
        }

        return $this->redirect(['view', 'id' => $env->id]);
    }

    public function actionAddKey(int $envId)
    {
        $env = $this->findModel($envId);
        $prevIds = $env->getAddedUsersKeys();
        $env->load(Yii::$app->request->post());
        $receivedIds = $env->added_users_keys;
        $newIds = array_diff($receivedIds, $prevIds);
        if ($newIds) {
            $this->envService->addKey($env, $newIds);
            $env->added_users_keys = array_merge($prevIds, $newIds);
            $env->saveOrFail(true, ['added_users_keys']);
            Yii::$app->session->addFlash('success', 'Keys added');
        }

        $this->redirect(['view', 'id' => $env->id]);
    }

    public function actionRemoveAuth(int $id, int $timeout): Response
    {
        $env = $this->findModel($id);
        try {
            $this->envService->removeBasicAuth($env, $timeout);
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::getLogger()->log($e, Logger::LEVEL_ERROR);
        }

        return $this->redirect(['view', 'id' => $env->id]);
    }

    public function actionReload(int $id): Response
    {
        $env = $this->findModel($id);
        try {
            $this->envService->reload($env);
            Yii::$app->session->setFlash('success', 'Env successfully reloaded');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::getLogger()->log($e, Logger::LEVEL_ERROR);
        }

        return $this->redirect(['view', 'id' => $env->id]);
    }

    protected function findModel(int $id): UserEnvironments
    {
        $env = UserEnvironments::findOne($id);
        if ($env == null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if (!Yii::$app->getUser()->getIdentity()->isRoot() && Yii::$app->getUser()->getId() != $env->user_id) {
            throw new HttpException(403, "Owner error");
        }

        return $env;
    }
}
