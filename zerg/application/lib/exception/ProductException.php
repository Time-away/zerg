<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/31
 * Time: 17:20
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 404;
    public $msg = "指定的商品不存在，请检查参数";
    public $errorCode = 20000;
}