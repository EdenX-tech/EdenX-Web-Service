<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
//use app\common\validate\UserValidate;
use app\common\model\Userinfo as UserinfoModel;

/**
 *
 */
class Userinfo extends BaseController{

    public function myselfKnowledgeList() {
        $list = (new UserinfoModel())->myselfKnowledgeList();
        return self::showResCode('更新成功',$list);
    }

    public function myselfKnowledgeAnswerList() {
        $list = (new UserinfoModel())->myselfKnowledgeAnswerList();
        return self::showResCode('更新成功',$list);
    }

    public function getUserInfo() {
        $list = (new UserinfoModel())->getUserInfo();
        return self::showResCode('更新成功',$list);
    }

}