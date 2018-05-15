<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/31
 * Time: 17:03
 */

namespace app\api\controller\v1;

use app\api\model\Product as ProductModel;
use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;

class Product
{
    public function getRecent($count = 15)
    {
        (new Count())->goCheck();
        $result = ProductModel::getMostRecent($count);
        if ($result->isEmpty()) {
            throw new ProductException();
        }
        $result = $result->hidden(['summary']);
        return $result;
    }

    public function getAllInCategory($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if ($products->isEmpty()) {
            throw new ProductException();
        }
        $products = $products->hidden(['summary']);
        return $products;
    }

    public function getOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $result = ProductModel::getProductDetail($id);
        if (!$result) {
            throw new ProductException();
        }
        return $result;
    }

}