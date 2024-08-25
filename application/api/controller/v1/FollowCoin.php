<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\FollowCoinValidate;
use app\common\model\FollowCoin as FollowCoinModel;

/**
 * 币种
 */
class FollowCoin extends BaseController
{
	
	public function followCoinList(){
		$list = (new FollowCoinModel())->followCoinList();
		return self::showResCode('获取成功',$list);
	}

	public function toFollowCoin(){
		(new FollowCoinValidate())->goCheck('toFollowCoin');
		$list = (new FollowCoinModel())->toFollowCoin();
		return self::showResCode('获取成功',$list);
	}

	public function ToUnFollow(){
		(new FollowCoinValidate())->goCheck('ToUnFollow');
		$list = (new FollowCoinModel())->ToUnFollow();
		return self::showResCode('获取成功',$list);
	}
}