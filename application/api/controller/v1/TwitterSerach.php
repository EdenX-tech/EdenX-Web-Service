<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\UserValidate;
use app\common\model\TwitterSerach as TwitterSerachModel;

class TwitterSerach extends BaseController
{
    public function serachList(){
        $list = (new TwitterSerachModel())->serachList();
        return self::showResCode('获取成功',$list);
    }
}