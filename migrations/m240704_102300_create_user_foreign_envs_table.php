<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Handles the creation of table `{{%user_foreign_envs}}`.
 */
class m240704_102300_create_user_foreign_envs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_foreign_envs}}', [
            'id' => $this->primaryKey(),
            'user_id' => 'INTEGER NOT NULL REFERENCES "user" (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'name' => 'VARCHAR(255) NOT NULL',
            'params' => 'jsonb',
        ]);

        $this->addColumn(UserEnvironments::tableName(), 'foreign_related_services_id', 'INTEGER[]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_foreign_envs}}');
        $this->dropColumn(UserEnvironments::tableName(), 'foreign_related_services_id');
    }
}
