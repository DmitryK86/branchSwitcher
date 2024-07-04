<?php

namespace app\controllers;

use app\managers\EnvService;
use app\models\User;
use app\models\UserEnvironments;
use Yii;
use app\models\SharedEnv;
use app\models\forms\SharedEnvsForm;
use yii\log\Logger;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * SharedEnvsController implements the CRUD actions for SharedEnv model.
 */
class SharedEnvsController extends Controller
{
    private EnvService $envService;

    public function __construct($id, $module, $config = [], EnvService $envService)
    {
        parent::__construct($id, $module, $config);

        $this->envService = $envService;
    }

    public function behaviors(): array
    {
        return [
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
        $searchModel = new SharedEnvsForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => User::findAll(['status' => User::STATUS_ACTIVE])
        ]);
    }

    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate(int $id): Response
    {
        $sharedEnv = $this->findModel($id);
        $env = $sharedEnv->environment;
        foreach ($env->branches as $branch) {
            $env->branchesData[$branch->repository->code] = $branch->branch;
        }
        try {
            $this->envService->update($env);
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::getLogger()->log($e, Logger::LEVEL_ERROR);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionUpdateOne(int $id, string $repositoryCode, string $branchName, bool $runAutotest = false): Response
    {
        $sharedEnv = $this->findModel($id);
        $env = $sharedEnv->environment;
        try {
            $this->envService->updateOne($env, $repositoryCode, $branchName, $runAutotest);
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::getLogger()->log($e, Logger::LEVEL_ERROR);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionAddKey(int $envId): Response
    {
        $sharedEnv = $this->findModel($envId);
        $env = $sharedEnv->environment;
        $prevIds = $env->getAddedUsersKeys();
        $env->load(Yii::$app->request->post());
        $receivedIds = $env->added_users_keys;
        $newIds = array_diff($receivedIds, $prevIds);
        if (count($newIds) > UserEnvironments::MAX_USER_KEYS_PER_REQUEST) {
            Yii::$app->session->addFlash('error', sprintf('You can add only %d user-keys per request', UserEnvironments::MAX_USER_KEYS_PER_REQUEST));
        } else {
            if ($newIds) {
                $this->envService->addKey($env, $newIds);
                $env->added_users_keys = array_merge($prevIds, $newIds);
                $env->saveOrFail(true, ['added_users_keys']);
                Yii::$app->session->addFlash('success', 'Keys added');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionRemoveAuth(int $id, int $timeout): Response
    {
        $sharedEnv = $this->findModel($id);
        $env = $sharedEnv->environment;
        try {
            if ($timeout > UserEnvironments::MAX_REMOVE_AUTH_MINUTES) {
                throw new \Exception("Max timeout is " . UserEnvironments::MAX_REMOVE_AUTH_MINUTES);
            }
            $this->envService->removeBasicAuth($env, $timeout);
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::getLogger()->log($e, Logger::LEVEL_ERROR);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id): ?SharedEnv
    {
        $model = SharedEnv::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if (!Yii::$app->getUser()->getIdentity()->isRoot() && Yii::$app->getUser()->getId() != $model->renter_id) {
            throw new HttpException(403, "Owner error");
        }

        return $model;

    }
}
