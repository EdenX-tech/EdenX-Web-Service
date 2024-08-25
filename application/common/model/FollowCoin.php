<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
/**
 * 
 */
class FollowCoin extends Model
{
	// 获取用户关注coin列表
	public function followCoinList(){
        // 当前用户id
        $userId = request()->userId; 
        $list = db::name('follow_coin')->alias('fc')->join('coin c','fc.coin_id = c.id')->field('c.id as coin_id, c.title, c.symbol, c.logo, fc.user_id')->where(['fc.is_delete'=>0, 'fc.user_id'=>$userId])->select();
		// $list = $this->where(['is_delete'=>0, 'user_id'=>$userId])->select();
		return $list;
	}
	
	// 关注
	public function toFollowCoin(){
		$params = request()->param();
		$userId = request()->userId;
        $is_vip = request()->isVip;

//        判断当前币种是否是vip
        $coinVip = (new coin())->where('id',$params['coin_id'])->value('is_vip');
        if ($coinVip) { if ($is_vip != 1) TApiException('暂无权限，请先成为vip',40000); }
		// 判断是否已经关注过
		$resExist = $this->followExist($userId, $params['coin_id']);

		if ($resExist) {
			if ($resExist['is_delete'] == 0) TApiException('已关注过~',440432);
			
			$this->where('id', $resExist['id'])->update(['is_delete' => 0]);
		} else {
			$this->create(['coin_id' => $params['coin_id'],'user_id' => $userId]);
		}
	
		return true;
	}

	// 取消关注
	public function ToUnFollow(){
		$params = request()->param();
		$userId = request()->userId;

		// 判断是否已经关注过
		$resExist = $this->followExist($userId, $params['coin_id']);

		if (!$resExist) TApiException('非法操作~',440432);
			
		if ($resExist['is_delete'] == 1) TApiException('已取消过~',440432);

		$this->where('id', $resExist['id'])->update(['is_delete' => 1]);

		return true;
	
	}

//
    public function isFollowCoinCheck($user_id, $coin_id) {

    }

	public function followExist($user_id, $coin_id){
		return $this->where(['user_id'=>$user_id,'coin_id'=>$coin_id])->find();
	}

	// 获取关注数量
	public function followCount($coin_id){
		$count = $this->where(['coin_id' => $coin_id,'is_delete' => 0])->count();
		return $count;
	}
}