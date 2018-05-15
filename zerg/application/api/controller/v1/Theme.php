<?php
/**
 * Created by PhpStorm.
 * User: 38577
 * Date: 2018/3/31
 * Time: 9:44
 */

namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;

/**
 * Class Theme 主题控制器
 * @package app\api\controller\v1
 */
class Theme
{
    /**
     * @url /theme?ids=id1,id2,id3,....
     * @return 一组theme模型
     */
    public function getSimpleList($ids = '')
    {
        (new IDCollection())->goCheck();
        $ids = explode(',', $ids);
        $result = ThemeModel::with('topicImg', 'headImg')->select($ids);
        if ($result->isEmpty()) {
            throw new ThemeException();
        }
        return $result;

    }

    /**
     * @url /theme/:id
     * @return 该主题的详情
     */
    public function getComplexOne($id)
    {
        (new IDMustBePositiveInt())->goCheck($id);
        $result = ThemeModel::getThemeWithProducts($id);
        if (!$result) {
            throw new ThemeException();
        }
        return $result;
    }
}