<?php
namespace common\repositories;

use common\models\Attachment;
use yii\web\NotFoundHttpException;

interface IAttachmentRepository
{
    /**
     * @param int $id
     * @return Attachment
     * @throws NotFoundHttpException
     */
    public function getById(int $id): Attachment;

    /**
     * @param Attachment $attachment
     * @throws \DomainException
     */
    public function save(Attachment $attachment);
}