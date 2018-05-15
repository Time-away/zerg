<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/2
 * Time: 19:37
 */

namespace app\lib\exception;


class ForBiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 100001;
}