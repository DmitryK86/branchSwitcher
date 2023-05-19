<?php

namespace app\models;

use app\models\aware\ActiveRecordAware;
use Yii;

/**
 * This is the model class for table "user_environment_branches".
 *
 * @property int $id
 * @property int $user_environment_id
 * @property int $repository_id
 * @property string $branch
 * @property string $created_at
 * @property bool $active
 *
 * @property Repository $repository
 * @property UserEnvironments $userEnvironment
 */
class UserEnvironmentBranches extends \yii\db\ActiveRecord
{
    use ActiveRecordAware;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_environment_branches';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_environment_id', 'repository_id'], 'required'],
            [['user_environment_id', 'repository_id'], 'integer'],
            [['repository_id'], 'exist', 'skipOnError' => true, 'targetClass' => Repository::className(), 'targetAttribute' => ['repository_id' => 'id']],
            [['user_environment_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserEnvironments::className(), 'targetAttribute' => ['user_environment_id' => 'id']],
            [['branch'], 'required'],
            [['active'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_environment_id' => 'User Environment ID',
            'repository_id' => 'Repository ID',
            'branch' => 'Branch name',
            'active' => 'Active'
        ];
    }

    /**
     * Gets query for [[Repository]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRepository()
    {
        return $this->hasOne(Repository::className(), ['id' => 'repository_id']);
    }

    /**
     * Gets query for [[UserEnvironment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserEnvironment()
    {
        return $this->hasOne(UserEnvironments::className(), ['id' => 'user_environment_id']);
    }
}
