<?php

namespace app\controllers;

use app\models\ProdBranchesLog;
use Yii;
use app\models\ProdBranch;
use app\models\forms\ProdBranchSearchForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProdBranchController implements the CRUD actions for ProdBranch model.
 */
class ProdBranchController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'matchCallback' => function ($rule, $action) {
                            $identity = Yii::$app->user->identity;
                            return !Yii::$app->user->isGuest && ($identity->isRoot() || $identity->isReleaseManager());
                        }
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

    /**
     * Lists all ProdBranch models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProdBranchSearchForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ProdBranch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProdBranch();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Prod branch created');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProdBranch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $prevBranchName = $model->branch_name;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ProdBranchesLog::log($model, Yii::$app->user->identity, $prevBranchName);
            Yii::$app->session->setFlash('success', 'Prod branch updated');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProdBranch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProdBranch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProdBranch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProdBranch::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
