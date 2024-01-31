<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Class m240131_073335_alter_user_env_add_column_is_persist
 */
class m240131_073335_alter_user_env_add_column_is_persist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironments::tableName(), 'is_persist', 'BOOLEAN DEFAULT FALSE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironments::tableName(), 'is_persist');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240131_073335_alter_user_env_add_column_is_persist cannot be reverted.\n";

        return false;
    }
    */
}
