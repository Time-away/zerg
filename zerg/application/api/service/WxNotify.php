<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/6
 * Time: 21:21
 */

namespace app\api\service;

use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        if($data['result_code'] == 'SUCCESS'){
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try {
                $order = OrderModel::where('order_no','=',$orderNo)->lock(true)->find();
                //1检测库存,2如果订单状态为待支付,修改支付状态,3减少库存
                if($order->status == 1){
                    $orderService = new OrderService();
                    $stockStatus = $orderService->checkOrderStock($order->id);
                    if($stockStatus['pass']){
                        //修改订单状态 (已支付)
                        $this->updateOrderStatus($order->id,true);
                        //减少库存
                        $this->reduceStock($stockStatus);
                    }else{
                        //修改订单状态 (已支付 但库存不足)
                        $this->updateOrderStatus($order->id,false);
                    }
                }
                Db::commit();
                return true;
            }catch (Exception $ex){
                Db::rollback();
                Log::error($ex);
                return false;
            }
        }else{
            return true;
        }
    }

    //修改订单状态
    private function updateOrderStatus($orderID,$success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id','=',$orderID)->update(['status'=>$status]);
    }

    //减少库存
    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus as $singlePStatus){
            Product::where('id','=',$singlePStatus['id'])->setDec('stock',$singlePStatus['count']);
        }
    }


}