<?php

namespace app\models\forms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CommandTemplate;

/**
 * CommandTemplateSearchForm represents the model behind the search form of `app\models\CommandTemplate`.
 */
class CommandTemplateSearchForm extends CommandTemplate
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'project_id'], 'integer'],
            [['name', 'action'], 'safe'],
            [['enabled'], 'boolean'],
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
        $query = CommandTemplate::find();

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
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'template', $this->template])
            ->orderBy('id DESC');

        return $dataProvider;
    }
}
