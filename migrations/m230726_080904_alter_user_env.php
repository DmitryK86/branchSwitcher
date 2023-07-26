<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Class m230726_080904_alter_user_env
 */
class m230726_080904_alter_user_env extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironments::tableName(), 'related_services_id', 'INTEGER[]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironments::tableName(), 'related_services_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230726_080904_alter_user_env cannot be reverted.\n";

        return false;
    }
    */
}
