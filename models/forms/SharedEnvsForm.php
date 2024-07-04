<?php

namespace app\models\forms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SharedEnv;

/**
 * SharedEnvsForm represents the model behind the search form of `app\models\SharedEnv`.
 */
class SharedEnvsForm extends SharedEnv
{
    public $project_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'owner_id', 'renter_id', 'environment_id'], 'integer'],
            [['config'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SharedEnv::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!\Yii::$app->getUser()->getIdentity()->isRoot()) {
            $this->renter_id = \Yii::$app->getUser()->getId();
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'renter_id' => $this->renter_id,
        ]);

        return $dataProvider;
    }
}
