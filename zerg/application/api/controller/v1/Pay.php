<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/6
 * Time: 15:22
 */

namespace app\api\controller\v1;

use app\api\service\Pay as PayService;
use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];

    //请求预订单 (微信服务器生成的订单)
    public function getPreOrder($id='')
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    //接收微信支付的异步通知
    public function receiveNotify()
    {
        //通知频率为15/15/30/180/1800/1800/1800/3600,单位:秒

        //1.检查库存量,超卖
        //2.更新这个订单的status状态
        //3.减库存
        //如果成功处理，返回微信成功处理信息,否则返回未成功处理信息
        //特点：post,xml格式,不会携带参数
        $notify = new WxNotify();
        $notify->Handle();
    }

}