<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/2
 * Time: 13:14
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\model\UserAddress;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
    ];

    public function createOrUpdateAddress()
    {
        $validata = new AddressNew();
        $validata->goCheck();
        //根据TOKEN获取Uid
        $uid = TokenService::getCurrentUid();
        //根据uid来查找用户数据,判断用户是否存在 如果不存在 抛出异常
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }
        //获取用户从客户端提交过来的地址信息
        $dataArray = $validata->getDataByRule(input('post.'));
        //根据用户地址信息是否存在 判断是添加还是修改
        $userAddress = $user->address;
        if(!$userAddress){
            //address() 带括号是添加  不带是修改
            $user->address()->save($dataArray);
        }else{
            $user->address->save($dataArray);
        }
        return json(new SuccessMessage(),201);
    }

    public function getUserAddress(){
        $uid = TokenService::getCurrentUid();
        $userAddress = UserAddress::where('user_id','=',$uid)->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }
}