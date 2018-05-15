<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/2
 * Time: 21:03
 */

namespace app\api\controller\v1;

use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    //用户在选则商品后，向API提交包含它所选择商品的相关信息
    //API在接收到信息后，需要检查订单相关商品的库存量
    //有库存，把订单数据存入数据库中=下单成功了，返回客户端消息，告诉用户可以支付了
    //调用支付接口进行支付
    //还需要再次进行库存量检测
    //服务器调用微信的支付接口进行支付
    //小程序根据服务器返回的结果拉起微信支付
    //微信会返回给我们一个支付结果(异步)
    //成功：也需要进行库存量的检查
    //成功：进行库存量的扣除，失败：返回一个支付失败的结果

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getDetail,getSummaryByUser']
    ];

    //订单分页
    public function getSummaryByUser($page=1,$size=15)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentTokenVar('uid');
        $pagingOrders = OrderModel::getSummaryByUser($uid,$page,$size);
        if(!$pagingOrders->isEmpty()){
            return [
                'data' => [],
                'current_page' => $pagingOrders->getCurrentPage()
            ];
        }
        $data = $pagingOrders->hidden(['snap_items','snap_address','prepay_id'])->toArray();
        return [
            'data' => $data,
            'current_page' => $pagingOrders->getCurrentPage()
        ];
    }

    public function getDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    //下单
    public function placeOrder(){
        (new OrderPlace())->goCheck();
        // products/a 表示获取客户端传来的 products数组
        $products = input('post.products/a');
        $uid = TokenService::getCurrentUid();
        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }
}