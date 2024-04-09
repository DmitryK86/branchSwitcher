<?php

use yii\db\Migration;
use app\models\UserEnvironments;

/**
 * Class m240409_064426_alter_user_envs_add_is_run_autotest
 */
class m240409_064426_alter_user_envs_add_is_run_autotest extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(UserEnvironments::tableName(), 'is_run_autotest', 'BOOLEAN DEFAULT FALSE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(UserEnvironments::tableName(), 'is_run_autotest');
    }
}
