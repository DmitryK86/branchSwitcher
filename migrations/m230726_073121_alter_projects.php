<?php

use yii\db\Migration;
use app\models\Project;

/**
 * Class m230726_073121_alter_projects
 */
class m230726_073121_alter_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TYPE project_type AS ENUM('main_project', 'service_project')");
        $this->addColumn(Project::tableName(), 'type', 'project_type DEFAULT \'main_project\'::project_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Project::tableName(), 'type');
        $this->execute('DROP TYPE project_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230726_073121_alter_projects cannot be reverted.\n";

        return false;
    }
    */
}
