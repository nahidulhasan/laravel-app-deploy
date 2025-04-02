<?php

namespace App\Repositories;

use App\Models\Category;

/**
 * Class CategoryRepository
 * @package App\Repositories
 */
class CategoryRepository  extends BaseRepository
{
    protected $modelName = Category::class;


    
    public function __construct(Category $model)
    {
        $this->model = $model;
    }
    public function getCategory($id=null)
    {
        $model = $this->getModel();
        return $model->with('subCategory')->where(['parent_id'=>null])->get();
    }


}
