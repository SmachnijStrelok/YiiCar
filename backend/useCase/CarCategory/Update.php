<?php
namespace backend\useCase\CarCategory;

use backend\forms\UpdateCategoryForm;
use common\models\CarCategory;
use common\repositories\ICategoryRepository;

class Update
{
    /** @var ICategoryRepository */
    private $repository;
    public function __construct(ICategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(UpdateCategoryForm $form): CarCategory
    {
        $category = $this->repository->getById($form->id);
        $category->title = $form->title;
        $category->logo_id = $form->logo_id;
        $this->repository->save($category);
        return $category;
    }
}