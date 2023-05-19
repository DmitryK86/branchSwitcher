<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "repository".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $code
 * @property string|null $api_code
 * @property string|null $api_token
 * @property bool|null $enabled
 * @property string $default_branch_name
 * @property string $version_control_provider
 */
class Repository extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'repository';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => true],
            [['name', 'code'], 'required'],
            [['name', 'code', 'api_code', 'default_branch_name', 'version_control_provider'], 'string', 'max' => 255],
            [['api_token'], 'string'],
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
            'default_branch_name' => 'Default branch name',
            'enabled' => 'Enabled',
            'api_code' => 'API code',
            'api_token' => 'API token',
            'version_control_provider' => 'Version control provider',
        ];
    }
}
