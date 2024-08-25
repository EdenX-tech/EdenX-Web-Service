<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use app\common\controller\ALiSendMail;
use think\Db;
// use app\common\controller\AliSMSController;

class User extends Model
{

    // 账号登录walletLogin
    public function walletLogin(){
        // 获取所有参数
        $params = request()->param();
        // 验证用户是否存在
        $user = $this->isExist($params);

        // 用户不存在
        if(!$user){
            // 用户主表
            $user = self::create([
                'address' => $params['address'],
                'username' => $params['address'],
                'userpic' => 'https://zansen.s3.ap-east-1.amazonaws.com/zansen/8f535e482023111516104219163.png'
            ]);

        }
        $user->logintype = 'wallet';
        // $user->openid = $user->openid ? true : false;
        $userarr = $user->toArray();
        $userarr['token'] = $this->CreateSaveToken($userarr);

        return $userarr;
    }

     public function editUserinfo(){
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $user_id=request()->userId;
        // 修改昵称
        $user = $this->get($user_id);

        if ($user->username != $params['name']) {

            if ($this->where(['username' => $params['name']])->find()) {
                return TApiException("当前用户名已被占用");
            } else {
                $user->username = $params['name'];
                $user->save();
            }

        }
        return true;
    }

    /**
     * 修改头像
     *
     * @param 
     * @return string
     **/
    public function editUserpic(){
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $userid=request()->userId;
        // 修改用户头像
        $user = self::get($userid);
        $user->userpic = $params['userpic'];

        if($user->save()) {
            return $user->userpic;
        }

        TApiException();
    }

//    更新
    public function updateUser(){
        $user_id = request()->userId;
        $userarr = $this->where('id',$user_id)->find();
        $userarr['password'] = $userarr['password'] ? true : false;

        if (!Cache::set(request()->userToken,$userarr,config('api.token_expire'))) TApiException();
        return $userarr;
    }
    
    /**
     * 绑定推特
     *
     * @return array
     */
    public function bindTwitter()
    {
        $param = request()->param();
        $user = $this->isExist(['provider' => $param['provider'], 'openid' => $param['openid']]);
        if (!$user) {
            $user = $this->get(request()->userId);
            
            $user->openid = $param['openid'];
            $user->oauth_token =  $param['oauth_token'];
            $user->oauth_token_secret = $param['oauth_token_secret'];
 
            $user->save();

            return true;
        }
        
        if (request()->userId == $user->id) return true;
        
        TApiException('已存在');

    }

    // 退出登录
    public function logout(){
        // 获取并清除缓存
        if (!Cache::pull(request()->userToken)) TApiException('Logged out',30006); return true;
    }

    // 生成并保存token
    public function CreateSaveToken($arr=[]){
        // 生成token
        $token = sha1(md5(uniqid(md5(microtime(true)),true)));
        $arr['token'] = $token;
        // 登录过期时间
        $expire =array_key_exists('expires_in',$arr) ? $arr['expires_in'] : config('api.token_expire');
        // 保存到缓存中
        if (!Cache::set($token,$arr,$expire)) TApiException();
        // 返回token
        return $token;
    }

     // 判断用户是否存在
    public function isExist($arr=[]){
        // dump(111);die;
        if(!is_array($arr)) return false;      
        // 用户address
        if (array_key_exists('address',$arr)) { // 用户名
            return $this->where('address',$arr['address'])->find();
        } 
        
        if (array_key_exists('provider',$arr)){
            $where = [
              'openid' => $arr['openid']
            ];
           return $this->where($where)->find();
        }
        return false;
    }

    // 验证密码
    public function checkPassword($password,$hash){
        if (!$hash) TApiException('Invalid password',20002);
        // 密码错误
        if(!password_verify($password,$hash)) TApiException('Invalid password',20002);
        return true;
    }

    // 用户是否被禁用
    public function checkStatus($arr,$isReget = false){
        $status = 1;
        $user = $this->find($arr['id'])->toArray();
        // 拿到status
        $status = $user['status'];
        if($status==0) TApiException('This user has been disabled',20001);
        return $arr;
    }


}