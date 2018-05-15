<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/28
 * Time: 11:08
 */

namespace app\lib\exception;


use think\Exception;
use Throwable;

class BaseException extends Exception
{
    //http请求的状态码
    public $code = 400;
    //错误信息
    public $msg = '参数错误';
    //自定义的错误码
    public $errorCode = 10000;

    public function __construct($param = [])
    {
        if (!is_array($param)){
            return;
        }

        if (array_key_exists('code',$param)){
            $this->code = $param['code'];
        }

        if (array_key_exists('msg',$param)){
            $this->msg = $param['msg'];
        }

        if (array_key_exists('errorCode',$param)){
            $this->errorCode = $param['errorCode'];
        }

    }
}