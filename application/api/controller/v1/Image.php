<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;
use think\Request;
use app\common\model\Image as ImageModel;

class Image extends BaseController
{
    public function uploadMore(){
        $data = (new ImageModel())->uploadMore();
        return self::showResCode('获取成功',['list'=>$data]);
    }
}