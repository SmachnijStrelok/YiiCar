<?php
namespace backend\forms;

use common\models\Attachment;
use yii\base\Model;

class AddCategoryForm extends Model
{
    public $title;
    public $logo_id;

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
                'targetAttribute' => ['logo_id' => 'id'],
                'message' => 'Id not found'
            ],
        ];
    }
}