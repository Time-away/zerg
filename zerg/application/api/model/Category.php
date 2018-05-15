<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/31
 * Time: 18:57
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['delete_time','create_time','update_time'];
    public function  Img(){
        return  $this->belongsTo('Image','topic_img_id','id');
    }
}