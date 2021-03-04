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
 * @property string $path
 * @property string $type
 * @property string $extension
 * @property string $mime
 * @property int    $created_at
 *
 * @property string $size [integer]
 */
class Attachment extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'integer'],
            [['name', 'type'], 'string', 'max' => 255],
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

    public static function getActiveQuery()
    {
        return StiActiveQuery::class;
    }

    public function upload()
    {
        if ($this->file) {
            if (!empty($this->file->type)) {
                $extension = strtolower(preg_replace(':\w+/(\w+)$:', '\1', $this->file->type));
            } else {
                $extension = strtolower(preg_replace('/.*\.(\w+)$/', '\1', $this->file->name));
            }

            $this->name = md5($this->file->name . time()) . '.' . $extension;
            if ($this->file->saveAs($this->path)) {
                $this->save(false);
                return true;
            }
        }
        return false;
    }
}