<?php

use yii\db\Migration;

/**
 * Class m220214_120802_alter_user_add_alias
 */
class m220214_120802_alter_user_add_alias extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'alias', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'alias');
    }
}
