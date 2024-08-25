<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\AiAnswerValidate;
use app\common\model\AiAnswer as AiAnswerModel;

/**
 * openai
 */
class AiAnswer extends BaseController
{

    public function answer(){
        (new AiAnswerValidate())->goCheck('answer');
        $list = (new AiAnswerModel())->answer();
        return self::showResCode('获取成功',$list);
    }

    public function upErrorAi(){
        $list = (new AiAnswerModel())->upErrorAi();
        return self::showResCode('获取成功',$list);
    }

    public function answerList(){
        $list = (new AiAnswerModel())->answerList();
        return self::showResCode('获取成功',$list);
    }

}