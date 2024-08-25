<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\KnowledgeBaseValidate;
use app\common\model\KnowledgeBase as KnowledgeBaseModel;

/**
 *
 */
class KnowledgeBase extends BaseController
{

    public function knowledgeList() {
        $list = (new KnowledgeBaseModel())->knowledgeList();
        return self::showResCode('获取成功',$list);
    }

    public function getTop() {
        $list = (new KnowledgeBaseModel())->getTop();
        return self::showResCode('更新成功',$list);
    }

    public function createKnowledge() {
        $list = (new KnowledgeBaseModel())->createKnowledge();
        return self::showResCode('更新成功',$list);
    }

    public function myselfKnowledgeList() {
        $list = (new KnowledgeBaseModel())->myselfKnowledgeList();
        return self::showResCode('更新成功',$list);
    }

    // 获取问题详情页
    public function knowledgeDetail() {
        (new KnowledgeBaseValidate())->goCheck('knowledgeDetail');
        $list = (new KnowledgeBaseModel())->knowledgeDetail();
        return self::showResCode('更新成功',$list);
    }

}