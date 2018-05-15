<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/2
 * Time: 13:22
 */

namespace app\api\validate;


class AddressNew extends BaseValidate
{
    protected  $rule = [
        'name' => 'require|isNotEmpty',
        'mobile' => 'require|isMobile',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'country' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty',
    ];
}