<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
//use app\common\validate\UserValidate;
use app\common\model\KnowledgeAnswer as KnowledgeAnswerModel;

class KnowledgeAnswer extends BaseController
{
    public function knowledgeAnswerList() {

        $list = (new KnowledgeAnswerModel())->knowledgeAnswerList();
        return self::showResCode('获取成功',$list);
    }

//    提交回答
    public function createKnowledgeAnswer() {
        $list = (new KnowledgeAnswerModel())->createKnowledgeAnswer();
        return self::showResCode('提交成功',$list);
    }
 }