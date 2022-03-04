<?php

namespace app\models\forms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SwitchLog;

/**
 * SwitchLogSearchForm represents the model behind the search form of `app\models\SwitchLog`.
 */
class SwitchLogSearchForm extends SwitchLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'created_at'], 'integer'],
            [['alias', 'from_branch', 'to_branch', 'status'], 'safe'],
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
        $query = SwitchLog::find()->orderBy('created_at DESC');

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]
        );

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
                                   'id' => $this->id,
                                   'user_id' => $this->user_id,
                                   'created_at' => $this->created_at,
                               ]);

        $query->andFilterWhere(['ilike', 'alias', $this->alias])
            ->andFilterWhere(['ilike', 'from_branch', $this->from_branch])
            ->andFilterWhere(['ilike', 'to_branch', $this->to_branch])
            ->andFilterWhere(['ilike', 'status', $this->status]);

        return $dataProvider;
    }
}
