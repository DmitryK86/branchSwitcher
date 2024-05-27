<?php

use yii\db\Migration;

/**
 * Class m240526_064452_create
 */
class m240526_064452_create_prod_branches extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('prod_branches', [
            'id' => $this->primaryKey(),
            'project_id' => 'INT NOT NULL REFERENCES project (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'repository_id' => 'INT NOT NULL REFERENCES repository (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'branch_name' => $this->string(255)->notNull(),
            'updated_at' => 'TIMESTAMPTZ DEFAULT NOW()',
        ]);

        $this->createIndex(
            'uidx_prod_branches_project_id_repository_id',
            'prod_branches',
            ['project_id', 'repository_id'],
            true
        );

        $this->createTable('prod_branches_log', [
            'id' => $this->primaryKey(),
            'user_id' => 'INT NOT NULL REFERENCES "user" (id) ON UPDATE CASCADE',
            'prod_branch_id' => 'INT NOT NULL REFERENCES prod_branches (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'prev_branch' => $this->string(255)->notNull(),
            'new_branch' => $this->string(255)->notNull(),
            'created_at' => 'TIMESTAMPTZ DEFAULT NOW()',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('prod_branches_log');
        $this->dropTable('prod_branches');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240526_064452_create cannot be reverted.\n";

        return false;
    }
    */
}
