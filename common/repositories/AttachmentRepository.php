<?php
namespace common\repositories;

use common\models\Attachment;
use yii\web\NotFoundHttpException;

class AttachmentRepository implements IAttachmentRepository
{
    /**
     * @param int $id
     * @return Attachment
     * @throws NotFoundHttpException
     */
    public function getById(int $id): Attachment
    {
        if(!$attachment = Attachment::findOne(['id' => $id])){
            throw new NotFoundHttpException("Attachment with id {$id}, not found!");
        }
        return $attachment;
    }

    /**
     * @param Attachment $attachment
     * @throws \DomainException
     */
    public function save(Attachment $attachment)
    {
        if(!$attachment->save()){
            throw new \DomainException("Can't save attachment!");
        }
    }
}