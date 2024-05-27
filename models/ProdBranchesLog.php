<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prod_branches_log".
 *
 * @property int $id
 * @property int $user_id
 * @property int $prod_branch_id
 * @property string $prev_branch
 * @property string $new_branch
 * @property string|null $created_at
 *
 * @property ProdBranch $prodBranch
 * @property User $user
 */
class ProdBranchesLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prod_branches_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'prod_branch_id', 'prev_branch', 'new_branch'], 'required'],
            [['user_id', 'prod_branch_id'], 'default', 'value' => null],
            [['user_id', 'prod_branch_id'], 'integer'],
            [['created_at'], 'safe'],
            [['prev_branch', 'new_branch'], 'string', 'max' => 255],
            [['prod_branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProdBranch::className(), 'targetAttribute' => ['prod_branch_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'prod_branch_id' => 'Prod Branch ID',
            'prev_branch' => 'Prev Branch',
            'new_branch' => 'New Branch',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ProdBranch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdBranch()
    {
        return $this->hasOne(ProdBranch::className(), ['id' => 'prod_branch_id']);
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

    public static function log(ProdBranch $prodBranch, User $user, string $prevBranchName): void
    {
        if ($prevBranchName === $prodBranch->branch_name) {
            return;
        }

        $model = new ProdBranchesLog();
        $model->prod_branch_id = $prodBranch->id;
        $model->user_id = $user->id;
        $model->prev_branch = $prevBranchName;
        $model->new_branch = $prodBranch->branch_name;
        if (!$model->save()) {
            throw new \Exception("Failed to save log for prod branch ID#{$prodBranch->id}. Details: " . $model->getFirstError());
        }
    }
}
