<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
// use app\common\validate\UserValidate;
use app\common\model\EnBanner as BannerModel;

/**
 * Banner
 */
class EnBanner extends BaseController
{
    public function bannerList(){
        $list = (new BannerModel())->bannerList();
        return self::showResCode('获取成功',$list);
    }
}