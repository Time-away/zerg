<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/1
 * Time: 10:53
 */

namespace app\lib\exception;


class WeChatException extends BaseException
{
    public $code = 400;
    public $msg = '微信服务器接口调用失败';
    public $errorCode = 999;
}