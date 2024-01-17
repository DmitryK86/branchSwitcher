<?php

namespace app\models;

use app\managers\MaxEnvCountResolver;
use app\models\aware\ActiveRecordAware;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ArrayExpression;
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
 * @property array $related_services_id
 * @property string $ip
 * @property array $added_users_keys
 * @property string $basic_auth_removed_till
 *
 * @property Project $project
 * @property User $user
 * @property UserEnvironmentBranches[] $branches
 * @property UserEnvironments[] $relatedServices
 * @property User[] $addedUsers
 */
class UserEnvironments extends ActiveRecord
{
    use ActiveRecordAware;

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_READY = 'ready';
    public const STATUS_ERROR = 'error';
    public const STATUS_DELETED = 'deleted';

    public const MAX_ENVS_PER_PROJECT = 1;

    public const MAX_REMOVE_AUTH_MINUTES = 60;

    public const EXPIRED_ENV_DAYS = 30;

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

            [['ip'], 'ip'],

            [['related_services_id'], 'each', 'rule' => ['integer']],
            [['related_services_id'], 'validateRelatedServices'],

            [['added_users_keys'], 'each', 'rule' => ['integer']],

            [['basic_auth_removed_till'], 'safe'],
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
            'ip' => 'IP',
            'related_services_id' => 'Related services',
            'basic_auth_removed_till' => 'Basic auth removed till'
        ];
    }

    public function validateEnvsCount($attribute, $params): void
    {
        if (!$this->isNewRecord) {
            return;
        }
        $count = self::find()->where(['user_id' => $this->user_id,'project_id' => $this->project_id, 'status' => [self::STATUS_READY, self::STATUS_IN_PROGRESS]])->count();
        $user = User::findOne(['id' => $this->user_id]);
        $maxCount = (new MaxEnvCountResolver())->resolveForUserAndProject($user, $this->project_id);
        if ($count >= $maxCount) {
            $this->addError($attribute, sprintf("Max envs count per project is exceeded (max %d)", $maxCount));
        }
    }

    public function validateRelatedServices($attribute, $params): void
    {
        if ($this->hasErrors()) {
            return;
        }
        if (!$this->isNewRecord) {
            return;
        }

        /** @var Project $currentProject */
        $currentProject = Project::find()->andWhere(['id' => $this->project_id])->one();
        foreach ($this->related_services_id as $serviceEnvId) {
            /** @var UserEnvironments $serviceEnv */
            $serviceEnv = self::find()->andWhere(['id' => $serviceEnvId])->one();
            if (!$serviceEnv) {
                $this->addError($attribute, "Service env with ID{$serviceEnvId} not found");
                return;
            }
            $serviceProject = $serviceEnv->project;

            if ($currentProject->code == $serviceProject->code) {
                $this->addError($attribute, "Relate same projects not permitted");
                return;
            }
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

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getBranches(): ActiveQuery
    {
        return $this->hasMany(UserEnvironmentBranches::className(), ['user_environment_id' => 'id'])->andWhere(['active' => true])->orderBy('id');
    }

    public function getAddedUsers(): ActiveQuery
    {
        return $this->hasMany(User::className(), ['id' => 'added_users_keys'])->andWhere(['status' => User::STATUS_ACTIVE])->orderBy('username');
    }

    public function getRelatedServices(): ActiveQuery
    {
        return $this->hasMany(UserEnvironments::className(), ['id' => 'related_services_id']);
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

    public static function findAvailableServicesForRelate(int $userId): array
    {
        return self::find()
            ->join('INNER JOIN', Project::tableName(), 'project.id = project_id')
            ->where(
                'project.type = :projectType AND user_id = :userId AND status = :status',
                [
                    ':projectType' => Project::TYPE_SERVICE,
                    ':userId' => $userId,
                    ':status' => self::STATUS_READY,
                ]
            )
            ->all();
    }

    public static function removeRelatedServices(UserEnvironments $deletedEnv): void
    {
        /** @var UserEnvironments[] $relatedEnvs */
        $relatedEnvs = self::find()->where(':service_env_id = ANY(related_services_id)', ['service_env_id' => $deletedEnv->id])->all();
        foreach ($relatedEnvs as $env) {
            $relatedIds = $env->related_services_id->getValue() ?? [];
            if (($key = array_search($deletedEnv->id, $relatedIds)) !== false) {
                unset($relatedIds[$key]);
                $env->related_services_id = $relatedIds;
                $env->saveOrFail(true, ['related_services_id']);
            }
        }
    }

    public function getAddedUsersKeys(): array
    {
        return $this->added_users_keys instanceof ArrayExpression ? $this->added_users_keys->getValue() : [];
    }
}
