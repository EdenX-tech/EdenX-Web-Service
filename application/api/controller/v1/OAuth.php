<?php

namespace app\api\controller\v1;

use app\common\controller\BaseController;

use app\lib\TwitterAuth;
use think\Request;

class OAuth extends BaseController
{
    // twitter授权登录页面
    public function twitterRedirect()
    {
        TwitterAuth::redirect();
    }

    // twitter授权回调
    public function twitterCallback()
    {
        $data = TwitterAuth::OAuth();
        return self::showResCode('授权成功', $data);
    }
     
    public function friendsShow()
    {
        $data = TwitterAuth::friendsShow();
        return self::showResCode('授权成功', $data);
    }
}   
