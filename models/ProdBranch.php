<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "prod_branches".
 *
 * @property int $id
 * @property int $project_id
 * @property int $repository_id
 * @property string $branch_name
 * @property string|null $updated_at
 *
 * @property Project $project
 * @property Repository $repository
 */
class ProdBranch extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'prod_branches';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['project_id', 'repository_id', 'branch_name'], 'required'],
            [['project_id', 'repository_id'], 'default', 'value' => null],
            [['project_id', 'repository_id'], 'integer'],
            [['updated_at'], 'safe'],
            [['branch_name'], 'string', 'max' => 255],
            [['project_id', 'repository_id'], 'unique', 'targetAttribute' => ['project_id', 'repository_id']],
            [['project_id'], 'validateProjectAndRepo'],
        ];
    }

    public function validateProjectAndRepo($attribute, $params, $validator)
    {
        $project = Project::findOne(['id' => $this->project_id]);
        if (!$project) {
            $this->addError($attribute, "Project with ID#{$this->project_id} does not exist");
            return;
        }
        $repo = Repository::findOne(['id' => $this->repository_id]);
        if (!$repo) {
            $this->addError($attribute, "Repository with ID#{$this->project_id} does not exist");
            return;
        }

        if (!in_array($this->repository_id, $project->repositories_id->getValue())) {
            $this->addError($attribute, "Project {$project->name} does not have repository {$repo->name}");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project',
            'repository_id' => 'Repository',
            'branch_name' => 'Branch Name',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getRepository(): ActiveQuery
    {
        return $this->hasOne(Repository::className(), ['id' => 'repository_id']);
    }
}
