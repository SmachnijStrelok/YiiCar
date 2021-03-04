<?php
namespace backend\useCase\CarCategory;

use backend\forms\AddCategoryForm;
use common\models\CarCategory;
use common\repositories\ICategoryRepository;

class Add
{
    /** @var ICategoryRepository */
    private $repository;
    public function __construct(ICategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AddCategoryForm $form)
    {
        $category = new CarCategory();
        $category->title = $form->title;
        $category->logo_id = $form->logo_id;
        $this->repository->save($category);
    }
}