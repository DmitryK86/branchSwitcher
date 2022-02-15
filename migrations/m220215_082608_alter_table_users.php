<?php

use yii\db\Migration;

/**
 * Class m220215_082608_alter_table_users
 */
class m220215_082608_alter_table_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('CREATE UNIQUE INDEX user_alias_uindex ON "user" (alias)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220215_082608_alter_table_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220215_082608_alter_table_users cannot be reverted.\n";

        return false;
    }
    */
}
