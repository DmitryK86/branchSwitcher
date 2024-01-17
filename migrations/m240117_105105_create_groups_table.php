<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%groups}}`.
 */
class m240117_105105_create_groups_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%groups}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->unique(),
            'params' => $this->string(),
            'enabled' => $this->boolean()->defaultValue(true),
        ]);

        $this->addColumn('user', 'group_id', 'INT DEFAULT NULL REFERENCES groups (id) ON UPDATE CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'group_id');
        $this->dropTable('{{%groups}}');
    }
}
