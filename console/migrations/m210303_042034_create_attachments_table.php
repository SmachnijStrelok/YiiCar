<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attachments}}`.
 */
class m210303_042034_create_attachments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%attachments}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'size' => $this->integer(),
            'extension' => $this->string(),
            'mime' => $this->string(),
            'type' => $this->string(),
            'path' => $this->string(),
            'created_at'=>$this->integer(),
        ]);

        $this->createIndex('idx-attachment-type', '{{%attachments}}', 'type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-attachment-type', '{{%attachments}}');
        $this->dropTable('{{%attachments}}');
    }
}
