<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/31
 * Time: 22:16
 */

namespace app\api\model;


class User  extends BaseModel
{
    public function address()
    {
        return $this->hasOne('UserAddress','user_id','id');
    }

    public static function getByOpenID($openid){
        $user = self::where('openid','=',$openid)->find();
        return $user;
    }
}