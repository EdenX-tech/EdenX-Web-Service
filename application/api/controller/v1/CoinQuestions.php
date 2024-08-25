<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\model\CoinQuestions as CoinQuestionsModel;

/**
 * 加入vip
 */
class CoinQuestions extends BaseController
{
    public function sendQuestion(){
        $list = (new CoinQuestionsModel())->sendQuestion();
        return self::showResCode('获取成功',$list);
    }

}