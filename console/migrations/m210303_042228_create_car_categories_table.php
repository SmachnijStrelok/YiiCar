<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car_categories}}`.
 */
class m210303_042228_create_car_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%car_categories}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'logo_id' => $this->integer()->null()
        ]);

        $this->createIndex(
            'idx-car_categories-logo_id',
            'car_categories',
            'logo_id'
        );

        $this->addForeignKey(
            'fk-car_categories-logo_id',
            'car_categories',
            'logo_id',
            'attachments',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-car_categories-logo_id',
            'car_categories'
        );
        $this->dropIndex(
            'idx-car_categories-logo_id',
            'car_categories'
        );
        $this->dropTable('{{%car_categories}}');
    }
}
