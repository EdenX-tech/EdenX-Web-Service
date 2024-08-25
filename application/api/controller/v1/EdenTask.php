<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\EdenTaskValidate;
use app\common\model\EdenTask as EdenTaskModel;

/**
 * 任务
 */
class EdenTask extends BaseController
{

    public function taskVideoList(){
        $list = (new EdenTaskModel())->taskVideoList();
        return self::showResCode('OK',$list);
    }

    public function taskPostList(){
        $list = (new EdenTaskModel())->taskPostList();
        return self::showResCode('OK',$list);
    }

    public function taskAlllist(){
        (new EdenTaskValidate())->goCheck('taskAlllist');
        $list = (new EdenTaskModel())->taskAlllist();
        return self::showResCode('OK',$list);
    }

    public function taskDeatil(){
        (new EdenTaskValidate())->goCheck('taskDeatil');
        $list = (new EdenTaskModel())->taskDeatil();
        return self::showResCode('OK',$list);
    }

    public function startTasks(){
        (new EdenTaskValidate())->goCheck('startTasks');
        $list = (new EdenTaskModel())->startTasks();
        return self::showResCode('OK',$list);
    }

    public function underwayTask(){
        (new EdenTaskValidate())->goCheck('underwayTask');
        $list = (new EdenTaskModel())->underwayTask();
        return self::showResCode('OK',$list);
    }

    public function endTask(){
        (new EdenTaskValidate())->goCheck('endTask');
        $list = (new EdenTaskModel())->endTask();
        return self::showResCode('OK',$list);
    }

    public function ReceiveAward(){
        (new EdenTaskValidate())->goCheck('ReceiveAward');
        $list = (new EdenTaskModel())->ReceiveAward();
        return self::showResCode('OK',$list);
    }

    public function callBackReceiveAward(){
        (new EdenTaskValidate())->goCheck('callBackReceiveAward');
        $list = (new EdenTaskModel())->callBackReceiveAward();
        return self::showResCode('OK',$list);
    }
}