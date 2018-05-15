<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/26
 * Time: 15:31
 */

namespace app\api\model;

class Banner extends BaseModel
{
    protected $hidden = ['delete_time','update_time'];

    public function items()
    {
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBanerByID($id)
    {
        //原生查询
        //$result = Db::query('select * from banner_item where banner_id=?',[$id]);
        //return $result;

        //查询构造器
        //where3中查询方式(1数组 2闭包 3表达式)

        //where表达式查询
        //$result = Db::table('banner_item')->where("banner_id",'=',$id)->select();
        //return $result;

        //where中使用闭包
        //加入 ->fetchSql() sql语句不会直接生成  会打印出SQL语句
        //$result = Db::table('banner_item')->where(function ($query) use ($id){
        //$query->where('banner_id','=',$id);
        //})->select();
        //return $result;
        $banner = self::with(['items','items.img'])->find($id);
        return $banner;
    }

}