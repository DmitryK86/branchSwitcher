<?php

use yii\db\Migration;

/**
 * Class m240430_104455_add_command_template
 */
class m240430_104455_add_command_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('command_template', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->unique()->notNull(),
            'action' => $this->string(255)->notNull(),
            'template' => $this->text()->notNull(),
            'enabled' => $this->boolean()->defaultValue(true),
            'project_id' => 'INT NOT NULL REFERENCES project(id) ON UPDATE CASCADE ON DELETE CASCADE',
        ]);

        $this->execute("CREATE UNIQUE INDEX command_template_project_id_action_uidx ON command_template(action, project_id)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('command_template');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240430_104455_add_command_template cannot be reverted.\n";

        return false;
    }
    */
}
