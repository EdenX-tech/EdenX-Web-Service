<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class EnPostMedium extends Model
{
	public function newMediumList(){
        $user_id = request()->param('user_id') ? request()->param('user_id') : 0;
        $list = db::name('follow_coin')->alias('fc')->join('en_post_medium pm','fc.coin_id=pm.coin_id')->join('coin c','pm.coin_id=c.id')
            ->where([
                'fc.user_id' => $user_id,
                'fc.is_delete' => 0,
                'pm.is_delete' => 0
            ])
            ->field('pm.id as pm_id, pm.title, pm.title_cn, pm.content_en , pm.content_cn, pm.url as pm_url, c.id as c_id, c.symbol as c_symbol, c.logo')
            ->limit(10)
            ->select();
        return $list;
    }

    public function informationList(){
        $param = request()->param();
        $userId = request()->userId;
        $page =input('page')?input('page'):1;
        $pageSize =input('pageSize')?input('pageSize'):10;

        if ($param['coin_id']) $where['fc.coin_id'] = $param['coin_id'];
        $where['fc.user_id'] = $userId;
        $where['fc.is_delete'] = 0;
        $where['pm.is_delete'] = 0;
        $post_medium['list'] = db::name('follow_coin')->alias('fc')->join('en_post_medium pm','fc.coin_id=pm.coin_id')->join('coin c','pm.coin_id=c.id')->where($where)->where('pm.title', 'not null')->field('c.id as coin_id, c.title as ctitle, c.symbol as csymbol, c.logo, pm.id as pmid, pm.title as pmtitle, c.twitter_url as projectName, pm.create_time, pm.create_time, pm.url, pm.title_cn')->order('pm.create_time desc')
                                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                                ->toArray();
        $count = db::name('follow_coin')->alias('fc')->join('en_post_medium pm','fc.coin_id=pm.coin_id')->join('coin c','pm.coin_id=c.id')->where($where)->where('pm.title', 'not null')->count();
        $post_medium['list']['totalPage'] = ceil($count/$pageSize);

        return $post_medium;
    }

    public function mediumDetail(){
        $param = request()->param();
        $list = db::name('en_post_medium')
                  ->where('id',$param['pid'])
                  ->field('content_cn,create_time')
                  ->find();
        $list['time'] = date("Y-m-d", strtotime($list['create_time']));

        return $list;
    }
}