<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ArrayExpression;

/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property array $repositories_id
 * @property bool|null $enabled
 * @property string $params
 * @property string $type
 *
 * @property UserEnvironments[] $userEnvironments
 * @property Repository[] $repositories
 * @property CommandTemplate[] $commandTemplates
 */
class Project extends \yii\db\ActiveRecord
{
    private const TYPE_MAIN = 'main_project';
    public const TYPE_SERVICE = 'service_project';

    public const PROJECT_TYPES = [
        self::TYPE_MAIN => 'Main',
        self::TYPE_SERVICE => 'Service',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['repositories_id'], 'required'],
            [['repositories_id'], 'each', 'rule' => ['integer']],
            [['enabled'], 'boolean'],
            [['name', 'code'], 'string', 'max' => 255],
            [['params'], 'string'],
            [['type'], 'required'],
            [['type'], 'in', 'range' => array_keys(self::PROJECT_TYPES)],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'repositories_id' => 'Repositories',
            'enabled' => 'Enabled',
            'params' => 'Params',
            'type' => 'Type'
        ];
    }

    public function getUserEnvironments(): ActiveQuery
    {
        return $this->hasMany(UserEnvironments::className(), ['project_id' => 'id']);
    }

    public function getRepositories(): ActiveQuery
    {
        return $this->hasMany(Repository::className(), ['id' => 'repositories_id']);
    }

    public function getCommandTemplates(): ActiveQuery
    {
        return $this->hasMany(CommandTemplate::className(), ['project_id' => 'id']);
    }

    public function getCustomCommandTemplate(string $action): ?string
    {
        $filtered = array_filter($this->commandTemplates, function (CommandTemplate $template) use ($action) {
            return $template->action == $action;
        });

        $template = reset($filtered);

        return $template ? $template->template : null;
    }

    public function getRepositoriesIdsArray(): array
    {
        /** @var ArrayExpression $ids */
        $ids = $this->repositories_id;
        return $ids->getValue() ?: [];
    }

    public function isServiceProject(): bool
    {
        return self::TYPE_SERVICE == $this->type;
    }
}
