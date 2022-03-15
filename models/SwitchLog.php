<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "switch_log".
 *
 * @property int $id
 * @property int $user_id
 * @property string $alias
 * @property string $project
 * @property string|null $from_branch
 * @property string|null $to_branch
 * @property string|null $status
 * @property int $created_at
 *
 * @property User $user
 */
class SwitchLog extends \yii\db\ActiveRecord
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'switch_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'alias'], 'required'],
            [['user_id'], 'integer'],
            [['alias', 'from_branch', 'to_branch'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

            ['status', 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::getStatuses())],

            ['project', 'required'],
            ['project', 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'alias' => 'Alias',
            'from_branch' => 'From Branch',
            'to_branch' => 'To Branch',
            'status' => 'Status',
            'project' => 'Project',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_ERROR => 'Error',
        ];
    }
}
