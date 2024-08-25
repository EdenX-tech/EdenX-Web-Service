<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\model\EnPostForum as PostForumModel;

/**
 * 币种
 */
class EnPostForum extends BaseController
{
    public function newForumList(){
        $list = (new PostForumModel())->newForumList();
        return self::showResCode('获取成功',$list);
    }

    public function informationList(){
        $list = (new PostForumModel())->informationList();
        return self::showResCode('获取成功',$list);
    }

    public function forumDetail(){
        $list = (new PostForumModel())->forumDetail();
        return self::showResCode('获取成功',$list);
    }
}