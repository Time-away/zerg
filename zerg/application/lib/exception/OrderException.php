<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/3
 * Time: 15:28
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在,请检查ID';
    public $errorCode = 80000;
}