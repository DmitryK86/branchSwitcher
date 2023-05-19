<?php

use yii\db\Migration;
use app\models\Project;

/**
 * Class m230721_145935_alter_projects
 */
class m230721_145935_alter_projects extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Project::tableName(), 'params', 'VARCHAR(255)');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Project::tableName(), 'params');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230721_145935_alter_projects cannot be reverted.\n";

        return false;
    }
    */
}
