<?php

use yii\db\Migration;

/**
 * Class m220312_080103_alter_switch_log
 */
class m220312_080103_alter_switch_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('switch_log', 'project', 'VARCHAR(255)');

        $this->execute("UPDATE switch_log SET project=alias");
        $this->execute('UPDATE switch_log SET alias=(SELECT alias FROM "user" WHERE id = user_id)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220312_080103_alter_switch_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220312_080103_alter_switch_log cannot be reverted.\n";

        return false;
    }
    */
}
