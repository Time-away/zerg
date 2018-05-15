<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/6
 * Time: 16:19
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    // 待支付
    const UNPAID = 1;
    // 已支付
    const PAID = 2;
    // 已发货
    const DELIVERED = 4;
    // 已支付 但库存不足
    const PAID_BUT_OUT_OF = 4;

}