<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/25
 * Time: 21:25
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;


class Banner
{
    /**
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     */
    public function getBanner($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $banner = BannerModel::getBanerByID($id);
        //hidden 隐藏某些字段 visible 显示某些字段
        //$banner->visible(['id']);
        //$banner->hidden(['delete_time','update_time']);
        if(!$banner){
            throw  new BannerMissException();
        }
        return $banner;
    }
}