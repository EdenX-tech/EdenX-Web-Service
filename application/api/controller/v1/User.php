<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\UserValidate;
use app\common\model\User as UserModel;

/**
 * 
 */
class User extends BaseController
{
    public function updateUser(){
        $user = (new UserModel())->updateUser();
        return self::showResCode('更新成功',$user);
    }

    // 退出登录
    public function logout(){
        (new UserModel())->logout();
        return self::showResCodeWithOutData('退出成功');
    }

    public function walletLogin(){
        (new UserValidate())->goCheck('walletLogin');
        $list = (new UserModel())->walletLogin();
        return self::showResCode('OK',$list);
    }
    
    public function editUserpic(){
        $list = (new UserModel())->editUserpic();
        return self::showResCode('OK',$list);
    }
    
    public function editUserinfo(){
        $list = (new UserModel())->editUserinfo();
        return self::showResCode('OK',$list);
    } 
    
    public function bindTwitter(){
        $list = (new UserModel())->bindTwitter();
        return self::showResCode('OK',$list);
    }
}