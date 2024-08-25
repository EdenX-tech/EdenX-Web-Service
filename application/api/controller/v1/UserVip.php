<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\model\UserVip as UserVipModel;

/**
 * 加入vip
 */
class UserVip extends BaseController
{
    public function addVip(){
        $list = (new UserVipModel())->addVip();
        return self::showResCode('获取成功',$list);
    }

    public function vipEndTime(){
        $list = (new UserVipModel())->vipEndTime();
        return self::showResCode('获取成功',$list);
    }
}