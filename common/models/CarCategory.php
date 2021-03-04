<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;


/**
 * This is the model class for table "attachments".
 *
 * @property int    $id
 * @property string $title
 * @property int    $logo_id
 *
 * @property Attachment $logo
 */
class CarCategory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%car_categories}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'logo_id'], 'required'],
            [
                ['logo_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Attachment::class,
                'targetAttribute' => ['logo_id' => 'id']
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

}