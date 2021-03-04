<?php
namespace backend\controllers;

use backend\forms\AddCategoryForm;
use backend\forms\UpdateCategoryForm;
use backend\useCase\CarCategory\Add;
use backend\useCase\CarCategory\Update;
use common\repositories\ICategoryRepository;
use Yii;
use yii\web\Controller;

/**
 * Работа с файлами
 *
 * @package api\controllers
 */
class CategoryController extends Controller
{
    public $enableCsrfValidation = false;

    public function noAuthActions(): array
    {
        return ['download', 'upload', 'update', 'list'];
    }

    public function verbs()
    {
        return [
            'add' => ['POST'],
            'update' => ['PUT'],
            'delete' => ['DELETE']
        ];
    }

    public function actionAdd(Add $handler)
    {
        $form = new AddCategoryForm();
        if($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $handler->handle($form);
        }

        return $this->render('create', ['category_form' => $form]);
    }

    public function actionUpdate(Update $handler)
    {
        $form = new UpdateCategoryForm();
        if($form->load(Yii::$app->request->post(), '') && $form->validate()){
            $category = $handler->handle($form);
        }

        return json_encode($category->getAttributes());
    }


    public function actionList(ICategoryRepository $repository)
    {
        $categories = $repository->getAll();
        return $this->render('categories', ['categories' => $categories]);
    }
}
