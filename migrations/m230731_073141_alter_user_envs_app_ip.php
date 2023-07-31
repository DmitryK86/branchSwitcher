<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Class m230731_073141_alter_user_envs_app_ip
 */
class m230731_073141_alter_user_envs_app_ip extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironments::tableName(), 'ip', 'INET DEFAULT NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironments::tableName(), 'ip');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230731_073141_alter_user_envs_app_ip cannot be reverted.\n";

        return false;
    }
    */
}
