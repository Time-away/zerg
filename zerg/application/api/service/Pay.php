<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/6
 * Time: 15:33
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use think\Log;

//载入第三方微信支付类库
// extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $orderID;
    private $orderNO;

    public function __construct($orderID)
    {
        if (!$orderID) {
            throw new Exception('订单号不允许为空');
        }
        $this->orderID = $orderID;
    }

    public function pay()
    {
        //异常检测
        $this->checkOrderValidate();
        //进行库存量检测
        $orderService = new Order();
        $status = $orderService->checkOrderStock($this->orderID);
        if(!$status['pass']){
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    //生成微信预订单
    private function makeWxPreOrder($totalPrice)
    {
        //获取openid
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException([
                'msg' => 'openid获取失败'
            ]);
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('假日红人');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }

    //获取微信支付的签名
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS'){
            Log::record($wxOrder,'error');
            Log::record('获取微信预支付订单失败','error');
        }
        //保存prepay_id
        $this->recordPreOrder($wxOrder);
        //获取签名
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function sign($wxOrder)
    {
        $jsPayApiData = new \WxPayJsApiPay();
        $jsPayApiData->SetAppid(config('wx.app_id'));
        $jsPayApiData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0,1000));
        $jsPayApiData->SetNonceStr($rand);
        $jsPayApiData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsPayApiData->SetSignType('md5');
        $sign = $jsPayApiData->MakeSign();
        $rawValues = $jsPayApiData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    //保存prepay_id
    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id','=',$this->orderID)->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    //异常检测
    private function checkOrderValidate()
    {
        $order = OrderModel::where('id','=',$this->orderID)->find();
        //订单号不存在
        if(!$order){
            throw new OrderException();
        }
        //订单号存在 订单号与当前用户不匹配
        if(!Token::isValidateOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }
        //订单有可能已经被支付了
        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg' => '订单状态已支付',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }
        //获取订单号
        $this->orderNO = $order->order_no;
        return true;
    }

}