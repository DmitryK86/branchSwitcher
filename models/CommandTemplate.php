<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "command_template".
 *
 * @property int $id
 * @property string $name
 * @property string $action
 * @property string $template
 * @property bool|null $enabled
 * @property int $project_id
 *
 * @property Project $project
 *
 */
class CommandTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'command_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'template', 'action'], 'required'],
            [['template', 'action'], 'string'],
            [['enabled'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['project_id'], 'integer']
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
            'action' => 'Action',
            'template' => 'Template',
            'enabled' => 'Enabled',
            'project_id' => 'Project',
        ];
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
