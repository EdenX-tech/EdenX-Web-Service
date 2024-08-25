<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class CoinClass extends Model
{
    //    获取币种分类
    public function coinClassList(){
        $list = $this->where('is_delete',0)->select();
        return $list;
    }

    // 全部
    public function coinList(){
        $user_id = request()->param('user_id') ? request()->param('user_id') : 0;
        $coin_class_id = request()->param('coin_class_id') ? request()->param('coin_class_id') : 1;
        $list = db::name('coin')
                  ->alias('c')
                  ->join('coin_class cc','c.coin_class_id = cc.id')
                  ->where([
                      'cc.is_delete' => 0,
                      'c.is_delete' => 0,
                      'c.coin_class_id' => $coin_class_id
                  ])
                  ->field('c.*,cc.title as coin_type')
                  ->order('c.sort desc')
                  ->select();
        $follow_coin = array();
        if ($user_id) {
            $follow_coin = (new FollowCoin())->where(['user_id'=>$user_id,'is_delete'=>0])->select();
        }

        foreach ($list as $key => $value) {
            $list[$key]['follow_status'] = 0;
            if ($follow_coin) {
                foreach ($follow_coin as $k => $v) {
                    if ($value['id'] == $v['coin_id']) {
                        $list[$key]['follow_status'] = 1;
                    }
                }
            }
//            $list[$key]['informationCount'] = $this->getCoinCount($value['id']);
//            $list[$key]['future'] = 0;
//            $list[$key]['followCount'] = (new FollowCoin())->followCount($value['id']);
        }

        return $list;
    }
}