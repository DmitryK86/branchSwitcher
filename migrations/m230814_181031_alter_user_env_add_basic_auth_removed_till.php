<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Class m230814_181031_alter_user_env_add_basic_auth_removed_till
 */
class m230814_181031_alter_user_env_add_basic_auth_removed_till extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironments::tableName(), 'basic_auth_removed_till', 'TIMESTAMP DEFAULT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironments::tableName(), 'basic_auth_removed_till');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230814_181031_alter_user_env_add_basic_auth_removed_till cannot be reverted.\n";

        return false;
    }
    */
}
