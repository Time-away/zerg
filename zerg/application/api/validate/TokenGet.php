<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/31
 * Time: 22:10
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '没有Code无法获取Token'
    ];
}