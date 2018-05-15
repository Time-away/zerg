<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/25
 * Time: 23:16
 */

namespace app\api\validate;


class IDMustBePositiveInt extends BaseValidate
{

    protected $rule = [
        'id' => 'require|isPositiveInteger'
    ];

    protected $message = [
        'id' => 'id必须是正整数'
    ];

}