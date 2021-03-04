<?php
namespace backend\forms;

use common\models\Attachment;
use common\models\CarCategory;
use yii\base\Model;

class UpdateCategoryForm extends Model
{
    public $id;
    public $title;
    public $logo_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title', 'logo_id'], 'required'],
            [
                ['id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => CarCategory::class,
                'targetAttribute' => ['id' => 'id'],
                'message' => 'Id not found'
            ],
            [
                ['logo_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Attachment::class,
                'targetAttribute' => ['logo_id' => 'id'],
                'message' => 'Id not found'
            ],
        ];
    }
}