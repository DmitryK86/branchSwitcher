<?php

namespace app\models\forms;

use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserEnvironments;

/**
 * UserEnvironmentsSearchForm represents the model behind the search form of `app\models\UserEnvironments`.
 */
class UserEnvironmentsSearchForm extends UserEnvironments
{
    private User $user;

    public function __construct(User $user, $config = [])
    {
        parent::__construct($config);

        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'project_id'], 'integer'],
            [['environment_code', 'created_at', 'updated_at', 'status', 'is_persist'], 'safe'],
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
        $query = UserEnvironments::find();

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
            'user_id' => $this->user->isRoot() ? $this->user_id : $this->user->id,
            'project_id' => $this->project_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'is_persist' => $this->is_persist,
        ]);

        $query->andFilterWhere(['ilike', 'environment_code', $this->environment_code]);

        $query->orderBy('id desc');

        return $dataProvider;
    }
}
