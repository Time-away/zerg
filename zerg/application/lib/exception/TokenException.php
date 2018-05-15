<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/1
 * Time: 13:06
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'Token已过期或无效Token';
    public $errorCode = 10001;
}