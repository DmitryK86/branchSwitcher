<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Class m230801_145203_alter_user_env
 */
class m230801_145203_alter_user_env extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironments::tableName(), 'added_users_keys', 'INTEGER[]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironments::tableName(), 'added_users_keys');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230801_145203_alter_user_env cannot be reverted.\n";

        return false;
    }
    */
}
