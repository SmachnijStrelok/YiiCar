<?php
namespace common\repositories;

use common\models\Car;
use yii\web\NotFoundHttpException;

interface ICarRepository
{
    /**
     * @param int $id
     * @return Car
     * @throws NotFoundHttpException
     */
    public function getById(int $id): Car;

    /** @return Car[] */
    public function findByCategoryId(int $categoryId): array;

    /**
     * @param Car $car
     * @throws \DomainException
     */
    public function save(Car $car);
}