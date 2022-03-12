<?php

namespace app\models\forms;

use yii\base\Model;

class SwitchForm extends Model
{
    public $alias;
    public $project;

    public function rules()
    {
        return [
            [['alias', 'project'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'alias' => 'Выбери стейдж',
            'project' => 'Выбери проект',
        ];
    }
}
