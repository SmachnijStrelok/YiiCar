<?php
namespace backend\controllers;

use common\models\Attachment;
use common\models\Car;
use common\models\CarCategory;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Работа с файлами
 *
 * @package api\controllers
 */
class CarController extends Controller
{
    public $enableCsrfValidation = false;
    public function noAuthActions(): array
    {
        return ['download', 'upload', 'update'];
    }

    public function verbs()
    {
        return [
            'add' => ['POST'],
            'update' => ['PUT'],
            'delete' => ['DELETE']
        ];
    }

    public function actionAdd()
    {
        $car = new Car();
        $car->name = Yii::$app->request->getBodyParam('name');
        $car->color = Yii::$app->request->getBodyParam('color');
        $car->equipment = Yii::$app->request->getBodyParam('equipment');
        $car->description = Yii::$app->request->getBodyParam('description');
        $car->availability = Yii::$app->request->getBodyParam('availability');
        $car->price = Yii::$app->request->getBodyParam('price');
        $car->logo_id = Yii::$app->request->getBodyParam('logo_id');
        $car->category_id = Yii::$app->request->getBodyParam('category_id');

        if (!$car->save()) {
            var_dump($car->getFirstErrors());die;
            throw new BadRequestHttpException('Save error');
        }

        return json_encode($car->getAttributes());
    }

    public function actionUpdate()
    {
        $carId = Yii::$app->request->getBodyParam('id');
        $car = Car::findOne(['id' => $carId]);
        if(!$car){
            throw new NotFoundHttpException('Car not found');
        }

        $car->name = Yii::$app->request->getBodyParam('name');
        $car->color = Yii::$app->request->getBodyParam('color');
        $car->equipment = Yii::$app->request->getBodyParam('equipment');
        $car->description = Yii::$app->request->getBodyParam('description');
        $car->availability = Yii::$app->request->getBodyParam('availability');
        $car->price = Yii::$app->request->getBodyParam('price');
        $car->logo_id = Yii::$app->request->getBodyParam('logo_id');
        $car->category_id = Yii::$app->request->getBodyParam('category_id');

        if (!$car->save()) {
            var_dump($car->getFirstErrors());die;
            throw new BadRequestHttpException('Save error');
        }

        return json_encode($car->getAttributes());
    }

    public function actionGet($id)
    {
        $id = Yii::$app->request->getQueryParam('id');
        $car = Car::findOne(['id' => $id]);
        return $this->render('car', ['car' => $car]);
    }

    public function actionList()
    {
        $cars = Car::find()->all();
        return $this->render('cars', ['cars' => $cars]);
    }

    public function actionIn_category($id)
    {
        $categoryId = Yii::$app->request->getQueryParam('id');
        $cars = Car::findAll(['category_id' => $categoryId]);
        return $this->render('cars', ['cars' => $cars]);
    }
}
