<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/1
 * Time: 12:49
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForBiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken()
    {
        //用三组随机字符串  进行md5加密
        //1 获取随机字符串
        $randChars = getRandChar(32);
        //2 当前时间戳
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //3 salt 盐
        $salt = config('secure.token_salt');
        return md5($randChars . $timestamp . $salt);
    }

    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new Exception("尝试获取的TOKEN变量不存在");
            }
        }
    }

    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    /**
     * 权限重构前置方法
     * 用户和管理员都能访问的权限
     */
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{
                throw new ForBiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    /**
     * 权限重构前置方法
     * 只有用户才能访问的接口权限
     */
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope == ScopeEnum::User){
                return true;
            }else{
                throw new ForBiddenException();
            }
        }else{
            throw new TokenException();
        }
    }

    public static function isValidateOperate($checkUID)
    {
        if(!$checkUID){
            throw new Exception('检查UID时必须传入一个被检测的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkUID){
            return true;
        }else{
            return false;
        }
    }

    public static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if($exist){
            return true;
        }else{
            return false;
        }
    }
}