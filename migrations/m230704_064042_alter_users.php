<?php

use yii\db\Migration;

/**
 * Class m230704_064042_alter_users
 */
class m230704_064042_alter_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP INDEX user_alias_uindex");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230704_064042_alter_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230704_064042_alter_users cannot be reverted.\n";

        return false;
    }
    */
}
