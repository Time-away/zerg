<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/28
 * Time: 11:11
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code = 404;
    public $msg = "请求的Banner不存在";
    public $errorCode = 40000;
}