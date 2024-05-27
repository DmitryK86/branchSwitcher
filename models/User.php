<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ArrayExpression;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $role
 * @property string $username
 * @property string|null $auth_key
 * @property string $password_hash
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $alias
 * @property string $ssh_key
 * @property string $env_params
 * @property int $group_id
 * @property array|ArrayExpression $projects
 *
 * @property Group $group
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_UPDATE = 'update';

    const STATUS_BLOCKED = 0;
    const STATUS_ACTIVE = 1;

    const ROLE_ROOT = 'root';
    const ROLE_USER = 'user';
    const ROLE_RELEASE_MANAGER = 'release_manager';

    const ALIAS_DEVOPS = 'devops';

    public $password;
    public $password_repeat;

    public function getStatusName()
    {
        return ArrayHelper::getValue(self::getStatusesArray(), $this->status);
    }

    public static function getStatusesArray()
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_BLOCKED => 'Заблокирован',
        ];
    }

    public static function getRolesArray(): array
    {
        return [
            self::ROLE_USER => 'User',
            self::ROLE_ROOT => 'Root',
            self::ROLE_RELEASE_MANAGER => 'Release Manager',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role', 'username', 'password_hash'], 'required'],
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
            [['username', 'password_hash', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],

            [['email'], 'unique'],
            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'default', 'value' => null],

            [['username'], 'unique'],
            [['username'], 'filter', 'filter' => 'trim'],

            [['password', 'password_repeat'], 'required', 'except' => self::SCENARIO_UPDATE],
            [['password', 'password_repeat'], 'string', 'max' => 32, 'min' => 6],
            [['password', 'password_repeat'], 'filter', 'filter' => 'trim'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat'],

            ['status', 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::getStatusesArray())],

            ['role', 'in', 'range' => array_keys(self::getRolesArray())],

            ['ssh_key', 'string'],

            ['env_params', 'validateValidJson'],

            ['projects', 'each', 'rule' => ['integer']],

            ['group_id', 'required'],
            ['group_id', 'integer'],
        ];
    }

    public function validateValidJson($attribute, $params, $validator)
    {
        json_decode($this->{$attribute});
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->addError($attribute, "{$this->getAttributeLabel($attribute)} not valid json");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'alias' => 'Alias',
            'ssh_key' => 'SSH Key',
            'env_params' => 'Environment params',
            'projects' => 'Projects',
            'group_id' => 'Group',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('findIdentityByAccessToken is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function beforeValidate()
    {
        if ($this->password) {
            $this->setPassword($this->password);
        }

        return parent::beforeValidate();
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
            }
            return true;
        }
        return false;
    }

    public function isUser(): bool
    {
        return self::ROLE_USER === $this->role;
    }

    public function isRoot(): bool
    {
        return $this->role == self::ROLE_ROOT;
    }

    public function isReleaseManager(): bool
    {
        return self::ROLE_RELEASE_MANAGER === $this->role;
    }

    public function getProjects(): array
    {
        return $this->projects instanceof ArrayExpression ? $this->projects->getValue() : [];
    }

    public function isDevops(): bool
    {
        return $this->getGroupName() == self::ALIAS_DEVOPS;
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getGroupName(): ?string
    {
        return $this->group->name ?? null;
    }
}
