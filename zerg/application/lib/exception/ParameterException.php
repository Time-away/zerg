<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/28
 * Time: 13:30
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;
}