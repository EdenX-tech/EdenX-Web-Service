<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\EnPostDepthValidate;
use app\common\model\EnPostDepth as PostDepthModel;

/**
 * 深度文
 */
class EnPostDepth extends BaseController
{

    public function postDepthList(){
        (new EnPostDepthValidate())->goCheck('postDepthList');
        $list = (new PostDepthModel())->postDepthList();
        return self::showResCode('OK',$list);
    }

    public function depthDetail(){
        (new EnPostDepthValidate())->goCheck('depthDetail');
        $list = (new PostDepthModel())->depthDetail();
        return self::showResCode('OK',$list);
    }

    public function getRightDepth(){
        $list = (new PostDepthModel())->getRightDepth();
        return self::showResCode('OK',$list);
    }

    public function getCategoryList(){
        $list = (new PostDepthModel())->getCategoryList();
        return self::showResCode('OK',$list);
    }

    public function getSubcategoryList(){
//        (new EnPostDepthValidate())->goCheck('getSubcategoryList');
        $list = (new PostDepthModel())->getSubcategoryList();
        return self::showResCode('OK',$list);
    }

    public function createPost(){
        (new EnPostDepthValidate())->goCheck('createPost');
        $list = (new PostDepthModel())->createPost();
        return self::showResCode('OK',$list);
    }

    public function userPostList(){
        (new EnPostDepthValidate())->goCheck('userPostList');
        $list = (new PostDepthModel())->userPostList();
        return self::showResCode('OK',$list);
    }

    public function postDepthEdit(){
        (new EnPostDepthValidate())->goCheck('postDepthEdit');
        $list = (new PostDepthModel())->edit();
        return self::showResCode('OK',$list);
    }


    public function postDepthDelete(){
        (new EnPostDepthValidate())->goCheck('postDepthDelete');
        $list = (new PostDepthModel())->delete();
        return self::showResCode('OK',$list);
    }

}