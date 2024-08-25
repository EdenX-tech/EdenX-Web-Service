<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\KnowledgeBaseValidate;
use app\common\model\NavigationLinks as NavigationLinksModel;

/**
 *
 */
class NavigationLinks extends BaseController
{

    public function linksList() {
        $list = (new NavigationLinksModel())->linksList();
        return self::showResCode('获取成功',$list);
    }

}