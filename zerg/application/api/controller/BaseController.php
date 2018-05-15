<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/3
 * Time: 13:34
 */

namespace app\api\controller;

use app\api\service\Token as TokenService;
use think\Controller;

class BaseController extends Controller
{
    //用户和管理员接口权限校验  基本验证
    protected function checkPrimaryScope(){
        TokenService::needPrimaryScope();
    }

    //用户接口权限校验  高级验证
    protected function checkExclusiveScope(){
        TokenService::needExclusiveScope();
    }
}