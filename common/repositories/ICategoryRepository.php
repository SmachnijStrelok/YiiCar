<?php
namespace common\repositories;

use common\models\CarCategory;
use yii\web\NotFoundHttpException;

interface ICategoryRepository
{
    /**
     * @param int $id
     * @return CarCategory
     * @throws NotFoundHttpException
     */
    public function getById(int $id): CarCategory;

    /** @return CarCategory[] */
    public function getAll(): array;

    /**
     * @param CarCategory $category
     * @throws \DomainException
     */
    public function save(CarCategory $category);
}