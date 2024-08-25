<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\UserValidate;
use app\common\model\UserAward as UserAwardModel;

/**
 * 
 */
class UserAward extends BaseController
{
    public function addAward(){
        $list = (new UserAwardModel())->addAward();
        return self::showResCode('OK',$list);
    }

}