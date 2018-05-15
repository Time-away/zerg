<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/4
 * Time: 21:30
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = [
        'user_id', 'delete_time', 'update_time'
    ];

    protected $autoWriteTimestamp = true;

    //读取器
    protected function getSnapItemsAttr($vaule)
    {
        if(empty($vaule)){
            return null;
        }
        return json_decode($vaule);
    }

    protected function getSnapAddressAttr($vaule)
    {
        if(empty($vaule)){
            return null;
        }
        return json_decode($vaule);
    }

    public static function getSummaryByUser($uid, $page = 1, $size = 15)
    {
        $pagingData = self::where('user_id', '=', $uid)->order('create_time desc')->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }
}