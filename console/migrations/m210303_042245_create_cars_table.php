<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cars}}`.
 */
class m210303_042245_create_cars_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cars}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string(),
            'equipment' => $this->string(),
            'description' => $this->text(),
            'availability' => $this->string(),
            'price' => $this->integer(),
            'logo_id' => $this->integer()->null(),
            'category_id' => $this->integer()
        ]);

        $this->createIndex(
            'idx-cars-logo_id',
            'cars',
            'logo_id'
        );

        $this->addForeignKey(
            'fk-cars-logo_id',
            'cars',
            'logo_id',
            'attachments',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-cars-category_id',
            'cars',
            'category_id'
        );

        $this->addForeignKey(
            'fk-cars-category_id',
            'cars',
            'category_id',
            'car_categories',
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
            'fk-cars-logo_id',
            'cars'
        );
        $this->dropIndex(
            'idx-cars-logo_id',
            'cars'
        );

        $this->dropForeignKey(
            'fk-cars-category_id',
            'cars'
        );
        $this->dropIndex(
            'idx-cars-category_id',
            'cars'
        );

        $this->dropTable('{{%cars}}');
    }
}
