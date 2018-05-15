<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/31
 * Time: 18:54
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategories()
    {
        // all()等同于with()->select()
        $categories = CategoryModel::all([],'Img');
        if($categories->isEmpty()){
            throw new CategoryException();
        }
        return $categories;
    }
}