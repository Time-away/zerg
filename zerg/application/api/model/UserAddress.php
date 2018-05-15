<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/2
 * Time: 15:43
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden = [
        'id','delete_time','user_id'
    ];
}