<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
// use app\common\controller\AliSMSController;

class UserAward extends Model
{
    public function addAward()
    {
        $params = request()->param();
        $user_id = request()->userId;
        
        $add = $this->create([
                'user_id' => $user_id,
                'award' => $params['award'],
                'to_address' => $params['to_address'],
                'from_address' => $params['from_address']
            ]);
            
        if ($add) {
            $to_address_user_id = (new User())->where('address', $params['to_address'])->value('id');
            $add_user_award = (new User())->where('id', $to_address_user_id)->setInc('award',$params['award']);
            
            if ($add_user_award) return true;
        }
        
        TApiException('Award Error');
    }
}