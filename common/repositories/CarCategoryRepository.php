<?php
namespace common\repositories;

use common\models\CarCategory;
use yii\web\NotFoundHttpException;

class CarCategoryRepository implements ICategoryRepository
{
    /** @inheritDoc*/
    public function getById(int $id): CarCategory
    {
        if(!$category = CarCategory::findOne(['id' => $id])){
            throw new NotFoundHttpException("Car category with id {$id}, not found!");
        }
        return $category;
    }

    /** @inheritDoc*/
    public function save(CarCategory $category)
    {
        if(!$category->save()){
            throw new \DomainException("Can't save category!");
        }
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return CarCategory::find()->all();
    }
}