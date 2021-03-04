<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;


/**
 * This is the model class for table "attachments".
 *
 * @property int    $id
 * @property string $name
 * @property string $color
 * @property string $equipment
 * @property string $description
 * @property string $availability
 * @property string $price
 * @property int    $logo_id
 * @property int    $category_id
 *
 * @property Attachment $logo
 * @property CarCategory $category
 */
class Car extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cars}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'color', 'equipment', 'description', 'availability', 'price', 'category_id', 'logo_id'], 'required'],
            [
                ['logo_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Attachment::class,
                'targetAttribute' => ['logo_id' => 'id'],
                'message' => 'Id not found'
            ],
            [
                ['category_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CarCategory::class,
                'targetAttribute' => ['category_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Filename',
            'type' => 'Type',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogo()
    {
        return $this->hasOne(Attachment::class, ['id' => 'logo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CarCategory::class, ['id' => 'category_id']);
    }

}