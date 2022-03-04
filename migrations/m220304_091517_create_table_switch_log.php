<?php

use yii\db\Migration;

/**
 * Class m220304_091517_create_table_switch_log
 */
class m220304_091517_create_table_switch_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%switch_log}}', [
            'id' => $this->primaryKey(),
            'user_id' => 'INTEGER NOT NULL REFERENCES "user" (id) ON UPDATE CASCADE ON DELETE CASCADE',
            'alias' => $this->string()->notNull(),
            'from_branch' => $this->string(),
            'to_branch' => $this->string(),
            'status' => $this->string(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-user-id', '{{%switch_log}}', 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220304_091517_create_table_switch_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220304_091517_create_table_switch_log cannot be reverted.\n";

        return false;
    }
    */
}
