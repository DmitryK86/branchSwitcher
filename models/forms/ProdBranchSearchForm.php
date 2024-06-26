<?php

namespace app\models\forms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProdBranch;

/**
 * ProdBranchSearchForm represents the model behind the search form of `app\models\ProdBranch`.
 */
class ProdBranchSearchForm extends ProdBranch
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'project_id', 'repository_id'], 'integer'],
            [['branch_name', 'updated_at'], 'safe'],
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
        $query = ProdBranch::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'repository_id' => $this->repository_id,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'branch_name', $this->branch_name]);
        $query->orderBy('project_id ASC, repository_id ASC');

        return $dataProvider;
    }
}
