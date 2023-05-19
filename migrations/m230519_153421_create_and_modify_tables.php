<?php

use yii\db\Migration;

/**
 * Class m230519_153421_create_tables
 */
class m230519_153421_create_and_modify_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('project', [
            'id' => 'pk',
            'name' => 'VARCHAR(255) DEFAULT NULL UNIQUE',
            'code' => 'VARCHAR(255) DEFAULT NULL UNIQUE',
            'repositories_id' => 'INTEGER[]',
            'enabled' => 'BOOLEAN DEFAULT TRUE',
        ]);

        $this->execute("CREATE TYPE user_env_status AS ENUM('in_progress', 'ready', 'error', 'deleted')");
        $this->createTable('user_environments', [
            'id' => 'pk',
            'user_id' => 'INTEGER NOT NULL REFERENCES "user" (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            'project_id' => 'INTEGER NOT NULL REFERENCES "project" (id) ON UPDATE CASCADE ON DELETE RESTRICT',
            'environment_code' => 'VARCHAR(255) DEFAULT NULL UNIQUE',
            'status' => 'user_env_status DEFAULT \'in_progress\'::user_env_status',
            'created_at' => 'TIMESTAMP DEFAULT NOW()',
            'updated_at' => 'TIMESTAMP DEFAULT NOW()',
        ]);

        $this->createTable('repository', [
            'id' => 'pk',
            'name' => 'VARCHAR(255) DEFAULT NULL UNIQUE',
            'code' => 'VARCHAR(255) DEFAULT NULL UNIQUE',
            'default_branch_name' => 'VARCHAR(255)',
            'api_code' => 'VARCHAR(255)',
            'api_token' => 'TEXT',
            'version_control_provider' => 'VARCHAR(255)' ,
            'enabled' => 'BOOLEAN DEFAULT TRUE',
        ]);

        $this->createTable('user_environment_branches', [
            'id' => 'pk',
            'user_environment_id' => 'INTEGER NOT NULL REFERENCES "user_environments" (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'repository_id' => 'INTEGER NOT NULL REFERENCES "repository" (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'branch' => 'VARCHAR(255) NOT NULL',
        ]);

        $this->addColumn('user', 'ssh_key', 'TEXT DEFAULT NULL');
        $this->addColumn('user', 'env_params', 'jsonb DEFAULT NULL');
        $this->addColumn('user', 'projects', 'INTEGER[]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'ssh_key');
        $this->dropColumn('user', 'env_params');
        $this->dropColumn('user', 'projects');

        $this->dropTable('user_environment_branches');
        $this->dropTable('user_environments');
        $this->dropTable('project');
        $this->dropTable('repository');

        $this->execute('DROP TYPE user_env_status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230519_153421_create_tables cannot be reverted.\n";

        return false;
    }
    */
}
