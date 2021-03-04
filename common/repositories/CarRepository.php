<?php
namespace common\repositories;

use common\models\Car;
use yii\web\NotFoundHttpException;

class CarRepository implements ICarRepository
{
    /** @inheritDoc*/
    public function getById(int $id): Car
    {
        if(!$car = Car::findOne(['id' => $id])){
            throw new NotFoundHttpException("Car with id {$id}, not found!");
        }
        return $car;
    }

    /** @inheritDoc*/
    public function save(Car $car)
    {
        if(!$car->save()){
            throw new \DomainException("Can't save car!");
        }
    }

    /** @inheritDoc */
    public function findByCategoryId(int $categoryId): array
    {
        return Car::findAll(['category_id' => $categoryId]);
    }
}