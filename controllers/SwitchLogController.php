<?php

namespace app\controllers;

use Yii;
use app\models\SwitchLog;
use app\models\forms\SwitchLogSearchForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SwitchLogController implements the CRUD actions for SwitchLog model.
 */
class SwitchLogController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
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

    /**
     * Lists all SwitchLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SwitchLogSearchForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
