<?php

namespace app\models;

use app\models\aware\ActiveRecordAware;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_environments".
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property string|null $environment_code
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string $status
 * @property string $comment
 *
 * @property Project $project
 * @property User $user
 * @property UserEnvironmentBranches[] $branches
 */
class UserEnvironments extends ActiveRecord
{
    use ActiveRecordAware;

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_READY = 'ready';
    public const STATUS_ERROR = 'error';
    public const STATUS_DELETED = 'deleted';

    public const MAX_ENVS_PER_PROJECT = 3;

    public array $branchesData = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_environments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'project_id'], 'required'],
            [['user_id', 'project_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['environment_code'], 'string', 'max' => 255],
            [['environment_code'], 'unique'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['branchesData'], 'required'],
            [['status'], 'in', 'range' => array_keys(self::getStatuses())],

            [['project_id'], 'validateEnvsCount'],

            [['comment'], 'string'],
        ];
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'Env ID',
            'user_id' => 'User ID',
            'project_id' => 'Project ID',
            'environment_code' => 'Environment Code',
            'branches' => 'Branches',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'comment' => 'Comment',
        ];
    }

    public function validateEnvsCount($attribute, $params): void
    {
        if (!$this->isNewRecord) {
            return;
        }
        $count = self::find()->where(['user_id' => $this->user_id,'project_id' => $this->project_id, 'status' => [self::STATUS_READY, self::STATUS_IN_PROGRESS]])->count();
        if ($count >= self::MAX_ENVS_PER_PROJECT) {
            $this->addError($attribute, sprintf("Max envs count per project is exceeded (max %d)", self::MAX_ENVS_PER_PROJECT));
        }
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

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getBranches()
    {
        return $this->hasMany(UserEnvironmentBranches::className(), ['user_environment_id' => 'id'])->andWhere(['active' => true])->orderBy('id');
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_IN_PROGRESS => 'В обработке',
            self::STATUS_READY => 'Готов к использованию',
            self::STATUS_ERROR => 'Ошибка',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    public static function getStatusClass(string $status): string
    {
        $data = [
            self::STATUS_IN_PROGRESS => 'warning',
            self::STATUS_READY => 'success',
            self::STATUS_ERROR => 'danger',
            self::STATUS_DELETED => 'info',
        ];

        return $data[$status] ?? '';
    }

    public function isReady(): bool
    {
        return self::STATUS_READY == $this->status;
    }

    public function isInProgress(): bool
    {
        return self::STATUS_IN_PROGRESS == $this->status;
    }

    public function isError(): bool
    {
        return self::STATUS_ERROR == $this->status;
    }

    public function canBeDeleted(): bool
    {
        return $this->isReady() || $this->isError();
    }

    public function canBeUpdated(): bool
    {
        return $this->isReady() || $this->isError();
    }

    public function setInProgress(): void
    {
        $this->status = self::STATUS_IN_PROGRESS;
        if (!$this->save(true, ['status', 'updated_at'])) {
            throw new \Exception("Env status change error. Env #{$this->id}");
        }
    }
}
