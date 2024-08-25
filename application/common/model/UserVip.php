<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class UserVip extends Model
{

    public function addVip(){
        $user_id = request()->userId;
//      查看当前是否是vip
        $info = $this->isUserExists($user_id);
        if ($info) {
            $is_vip = $this->where(['user_id'=>$user_id, 'is_delete'=>0])->find();
            if ($is_vip) TApiException('已经是会员', 43000);
            $saveinfo = $this->save([
                'is_delete'  => 0,
                'create_time' => date("Y-m-d H:i:s", time()),
                'end_time' => date("Y-m-d H:i:s", strtotime("+30 day")),
            ],['user_id' => $user_id]);
        } else {
            $saveinfo = $this->save([
                'user_id' => $user_id,
                'is_delete'  => 0,
                'create_time' => date("Y-m-d H:i:s", time()),
                'end_time' => date("Y-m-d H:i:s", strtotime("+30 day")),
            ]);
        }
        if (!$saveinfo) TApiException('加入失败', 42000);
        //            更新日志
        $data = [
            'user_id' => $user_id,
            'create_time' => date("Y-m-d H:i:s", time()),
            'end_time' => date("Y-m-d H:i:s", strtotime("+30 day")),
        ];
        (new VipLog())->insert($data);
        (new User)->where('id', $user_id)->update(['is_vip'=>1]);

        return true;
    }

    public function isUserExists($user_id)
    {
       $info = $this->where(['user_id'=>$user_id])->find();
       return $info ? $info : false;
    }

    public function vipEndTime(){
        $user_id = request()->param('user_id');
        $vip_user = $this->where(['user_id'=>$user_id, 'is_delete'=>0])->find();

        return $vip_user ? $vip_user['end_time'] : '';

    }
}