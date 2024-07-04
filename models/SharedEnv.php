<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "shared_envs".
 *
 * @property int $id
 * @property int $owner_id
 * @property int $renter_id
 * @property int $environment_id
 * @property string|null $config
 *
 * @property User $owner
 * @property User $renter
 * @property UserEnvironments $environment
 */
class SharedEnv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shared_envs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner_id', 'renter_id', 'environment_id'], 'required'],
            [['owner_id', 'renter_id', 'environment_id'], 'default', 'value' => null],
            [['owner_id', 'renter_id', 'environment_id'], 'integer'],
            [['config'], 'safe'],
            [['owner_id', 'renter_id', 'environment_id'], 'unique', 'targetAttribute' => ['owner_id', 'renter_id', 'environment_id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['owner_id' => 'id']],
            [['renter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['renter_id' => 'id']],
            [['environment_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserEnvironments::className(), 'targetAttribute' => ['environment_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner_id' => 'Owner ID',
            'renter_id' => 'Renter ID',
            'environment_id' => 'Environment ID',
            'config' => 'Config',
        ];
    }

    /**
     * Gets query for [[Owner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }

    /**
     * Gets query for [[Renter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRenter()
    {
        return $this->hasOne(User::className(), ['id' => 'renter_id']);
    }

    /**
     * Gets query for [[Environment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEnvironment()
    {
        return $this->hasOne(UserEnvironments::className(), ['id' => 'environment_id']);
    }
}
