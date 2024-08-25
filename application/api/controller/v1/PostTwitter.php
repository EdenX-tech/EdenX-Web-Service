<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
// use app\common\validate\PostTwitterValidate;
use app\common\model\PostTwitter as PostTwitterModel;

/**
 * 推特
 */
class PostTwitter extends BaseController
{

    public function informationList(){
        $list = (new PostTwitterModel())->informationList();
        return self::showResCode('获取成功',$list);
    }

}