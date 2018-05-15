<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/2
 * Time: 11:59
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['product_id', 'delete_time', 'img_id'];

    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}