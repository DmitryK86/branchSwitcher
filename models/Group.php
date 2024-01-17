<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "groups".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $params
 * @property boolean $enabled
 *
 * @property User[] $users
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'groups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'params'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['enabled'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'params' => 'Params',
            'enabled' => 'Enabled',
        ];
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::className(), ['group_id' => 'id']);
    }
}
