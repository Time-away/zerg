<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/28
 * Time: 11:06
 */

namespace app\lib\exception;


use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $errorCode;
    private $msg;

    public function render(\Exception $e)
    {

        //如果是给客户返回的异常（分为两种 1给客户返回的异常 2服务器内部异常）
        if ($e Instanceof BaseException) {
            $this->code = $e->code;
            $this->errorCode = $e->errorCode;
            $this->msg = $e->msg;
        } else {
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->code = 500;
                $this->errorCode = 999;
                $this->msg = '服务器内部错误';
                $this->recordErrorLog($e);
            }
        }
        $request = Request::instance();
        $result = [
            'error_code' => $this->errorCode,
            'msg' => $this->msg,
            'request_url' => $request->url()
        ];
        return json($result, $this->code);
    }


    private function recordErrorLog(\Exception $e)
    {
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(), 'error');
    }

}