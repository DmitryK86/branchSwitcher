<?php

use yii\db\Migration;

/**
 * Class m240704_152133_create_table_shared_envs
 */
class m240704_152133_create_table_shared_envs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('shared_envs', [
            'id' => $this->primaryKey(),
            'owner_id' => 'INTEGER NOT NULL REFERENCES "user" (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'renter_id' => 'INTEGER NOT NULL REFERENCES "user" (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'environment_id' => 'INTEGER NOT NULL REFERENCES "user_environments" (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'config' => 'jsonb',
        ]);

        $this->createIndex('shared_envs_owner_id_renter_id_env_id_uidx', 'shared_envs', ['owner_id', 'renter_id', 'environment_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('shared_envs');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240704_152133_create_table_shared_envs cannot be reverted.\n";

        return false;
    }
    */
}
