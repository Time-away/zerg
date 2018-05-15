<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/3
 * Time: 14:27
 */

namespace app\api\service;

use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;

class Order
{
    //客户端传下单传来的products商品信息
    protected $oProducts;
    //通过$oProducts查询数据库得到的真实商品信息
    protected $products;

    protected $uid;

    /**
     * 下单
     */
    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
        //检测库存量
        $status = $this->getOrderStatus();
        //库存量检测失败
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        //创建订单
        $orderSnap = $this->orderSnap($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;

    }

    //外部调用的检测库存量方法
    public function checkOrderStock($orderID)
    {
        $oProducts = OrderProduct::where('order_id','=',$orderID)->select();
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }

    /**
     * 创建订单
     */
    private function createOrder($snap)
    {
        //开始事务
        Db::startTrans();
        try {
            $orderNo = self::makeOrderNo();
            $order = new \app\api\model\Order();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();
            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            //提交事务
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        } catch (Exception $ex) {
            //事务回滚
            Db::rollback();
            throw $ex;
        }

    }

    //生成订单号
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] .
            strtoupper(dechex(date('m'))) .
            date('d') .
            substr(time(), -5) .
            substr(microtime(), 2, 5) .
            sprintf('%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * 生成订单快照
     */
    private function orderSnap($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => '',
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        //序列化数组 存入数据库
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        //将订单商品的第一个信息 作为订单的名称
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }
        return $snap;
    }

    //获取订单快照的地址
    private function getUserAddress()
    {
        $userAddreess = UserAddress::where("user_id", '=', $this->uid)->find();
        if (!$userAddreess) {
            throw new UserException([
                'msg' => '用户收货地址不存在,下单失败',
                'errorCode' => 60001
            ]);
        }
        return $userAddreess->toArray();
    }

    //获取订单的真实状态 检测库存量
    private function getOrderStatus()
    {

        $status = [
            //订单库存量检测是否通过
            'pass' => true,
            //订单价格总和
            'orderPrice' => 0,
            //订单商品总数量（种类*单品数量）
            'totalCount' => 0,
            //保存订单所有商品的详细信息
            'pStatusArray' => []
        ];
        //用循环遍历做库存量对比
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'], $oProduct['count'], $this->products
            );
            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;

    }

    /**
     * 获取订单内一种产品的真实状态
     * $oPID  真实产品ID
     * $oCount  真实产品数量
     * $products  真实商品数组
     */
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;
        //小程序中单个订单历史详情记录
        $pStatus = [
            //当前商品ID
            'id' => null,
            //当前商品库存量
            'haveStock' => false,
            //当前商品数量
            'count' => 0,
            //当前商品名称
            'name' => '',
            //当前商品总价格
            'totalPrice' => 0
        ];
        for ($i = 0; $i < count($products); $i++) {
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            //客户端传递的product_id有可能根本不存在
            throw new OrderException([
                'msg' => 'id为' . $oPID . '商品不存在,创建订单失败'
            ]);
        } else {
            //检测库存量
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $oCount;
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }
            return $pStatus;
        }
    }

    //根据订单信息查找真实商品信息
    private function getProductsByOrder($oProducts)
    {
        $oPids = [];
        foreach ($oProducts as $oProduct) {
            array_push($oPids, $oProduct['product_id']);
        }
        $products = Product::all($oPids)->visible(['id', 'price', 'stock', 'name', 'main_img_url'])->toArray();
        return $products;
    }
}