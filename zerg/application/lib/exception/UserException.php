<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/2
 * Time: 14:59
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $msg = '用户不存在';
    public $errorCode = 60000;
}