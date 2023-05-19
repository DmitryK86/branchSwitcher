<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Class m230721_100233_alter_user_env
 */
class m230721_100233_alter_user_env extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironments::tableName(), 'comment', 'TEXT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironments::tableName(), 'comment');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230721_100233_alter_user_env cannot be reverted.\n";

        return false;
    }
    */
}
