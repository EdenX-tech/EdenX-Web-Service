<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\model\EnPostMedium as PostMediumModel;

/**
 * 币种
 */
class EnPostMedium extends BaseController
{
    public function newMediumList(){
        $list = (new PostMediumModel())->newMediumList();
        return self::showResCode('获取成功',$list);
    } 
    
    public function informationList(){
        $list = (new PostMediumModel())->informationList();
        return self::showResCode('获取成功',$list);
    }

    public function mediumDetail(){
        $list = (new PostMediumModel())->mediumDetail();
        return self::showResCode('获取成功',$list);
    }
}