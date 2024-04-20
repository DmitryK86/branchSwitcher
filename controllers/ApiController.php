<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\User;
use app\models\UserEnvironments;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApiController extends Controller
{
    public function init()
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        parent::init();
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['info'],
                'rules' => [
                    [
                        'actions' => ['info'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            $h = \Yii::$app->request->getHeaders();
                            if (!$h->has('authorization')) {
                                return false;
                            }

                            return $h->get('authorization') == \Yii::$app->params['publicApiKey'];
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionInfo(string $username)
    {
        $user = User::findOne(['username' => $username]);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $envs = UserEnvironments::findAll(['user_id' => $user->getId(), 'status' => UserEnvironments::STATUS_READY]);
        if (!$envs) {
            throw new HttpException(499, "User has no envs");
        }

        $response = [];
        foreach ($envs as $env) {
            $response[$env->environment_code] = [
                'project_name' => $env->project->code,
                'updated_at' => $env->updated_at,
            ];
        }

        return $response;
    }
}
