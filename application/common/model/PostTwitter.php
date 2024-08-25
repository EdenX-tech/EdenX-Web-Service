<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class PostTwitter extends Model
{
    //配置连接参数
    protected $connection = 'my_db_utf8mb4';
    
	public function informationList(){
        $param = request()->param();
        $userId = request()->userId;
        $page =input('page')?input('page'):1;
        $pageSize =input('pageSize')?input('pageSize'):10;

        if ($param['coin_id']) $where['fc.coin_id'] = $param['coin_id'];
        $where['fc.user_id'] = $userId;
        $where['fc.is_delete'] = 0;

        $post_twitter['list'] = db::name('follow_coin')->alias('fc')->join('post_twitter pt','fc.coin_id=pt.coin_id')->join('coin c','pt.coin_id=c.id')->where($where)->where('pt.content','not null')->field('c.id as coin_id, c.logo, c.title as ctitle, c.symbol as csymbol, pt.content_cn, pt.content as content_en, c.twitter_url as projectName, pt.create_time, pt.id as pt_id')->order('pt.create_time desc')
            ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
            ->toArray();
        $count = db::name('follow_coin')->alias('fc')->join('post_twitter pt','fc.coin_id=pt.coin_id')->join('coin c','pt.coin_id=c.id')->where($where)->where($where)->where('pt.content','not null')->count();
        $post_twitter['list']['totalPage'] = ceil($count/$pageSize);
        foreach ($post_twitter['list']['data'] as $key=>$val){
            $post_twitter['list']['data'][$key]['time'] = date("Y-m-d H:i:s", $val['create_time']);
            $post_twitter['list']['data'][$key]['imgs'] = $this->getImagesList($val['pt_id']);
            $post_twitter['list']['data'][$key]['content_cn'] = $val['content_cn'] ? $val['content_cn'] : $val['content_en'];

        }

        return $post_twitter;
    }

    public function getImagesList($coin_id){
        $image = db::name('post_twitter_img')
                   ->where('post_twitter_id', $coin_id)
                   ->field('img_url')
                   ->select();
        return $image;
    }
}