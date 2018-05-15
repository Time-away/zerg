<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/4/4
 * Time: 17:50
 */

namespace app\api\controller\v1;


class Test
{
    public function index()
    {

        $po = [
            [
                'name' => 'jack',
                'age' => 18
            ],
            [

                'name' => 'tom',
                'age' => 12
            ],
            [

                'name' => 'Ann',
                'age' => 22
            ]
        ];
        foreach ($po as $k => $p){
            ['sex'] = 'women';
        }
        dump($po);

    }
}