<?php

use yii\db\Migration;
use app\models\UserEnvironmentBranches;

/**
 * Class m230714_090203_aletr_user_env_branches
 */
class m230714_090203_aletr_user_env_branches extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironmentBranches::tableName(), 'created_at', 'TIMESTAMP DEFAULT NOW()');
        $this->addColumn(UserEnvironmentBranches::tableName(), 'active', 'BOOLEAN DEFAULT TRUE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironmentBranches::tableName(), 'created_at');
        $this->dropColumn(UserEnvironmentBranches::tableName(), 'active');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230714_090203_aletr_user_env_branches cannot be reverted.\n";

        return false;
    }
    */
}
