<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class EnPostForum extends Model
{
    public function newForumList(){
        $user_id = request()->param('user_id') ? request()->param('user_id') : 0;
        $list = db::name('follow_coin')->alias('fc')->join('en_post_forum pm','fc.coin_id=pm.coin_id')->join('coin c','pm.coin_id=c.id')
                  ->where([
                      'fc.user_id' => $user_id,
                      'fc.is_delete' => 0,
                      'pm.is_delete' => 0
                  ])
                  ->field('pm.id as pm_id, pm.title_cn, pm.title_en, pm.content_cn, pm.content_en, pm.url as pm_url, c.id as c_id, c.symbol as c_symbol')
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

        $post_forum['list'] = db::name('follow_coin')->alias('fc')->join('en_post_forum pf','fc.coin_id=pf.coin_id')->join('coin c','pf.coin_id=c.id')->where($where)->field('c.id as coin_id, c.logo, c.title as ctitle, c.symbol as csymbol,pf.id as pfid, pf.title_cn, pf.url, pf.create_time, pf.time')->order('pf.create_time desc')
                                  ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                                  ->toArray();
        $count = db::name('follow_coin')->alias('fc')->join('en_post_forum pf','fc.coin_id=pf.coin_id')->join('coin c','pf.coin_id=c.id')->where($where)->count();
        $post_forum['list']['totalPage'] = ceil($count/$pageSize);
//        foreach ($post_forum['list']['data'] as $key=>$val){
//            $post_forum['list']['data'][$key]['time'] = $val['create_time'] ? $val['create_time'] : $val['time'];
//            if (!$val['content_en']){
//                $post_forum['list']['data'][$key]['content_cn'] = stripslashes(trim(str_replace(array("\r\n", "\r", "\n", " ","'"), " ", $val['title_en'])));
//                $post_forum['list']['data'][$key]['content_en'] = stripslashes(trim(str_replace(array("\r\n", "\r", "\n", " ","'"), " ", $val['title_en'])));
//            } else {
//                $post_forum['list']['data'][$key]['content_cn'] = stripslashes(trim(str_replace(array("\r\n", "\r", "\n", " ","'"), " ", $val['content_cn'])));
//                $post_forum['list']['data'][$key]['content_en'] = stripslashes(trim(str_replace(array("\r\n", "\r", "\n", " ","'"), " ", $val['content_en'])));
//            }
//
//        }

        return $post_forum;
    }

    public function forumDetail(){
        $param = request()->param();
        $list = db::name('en_post_forum')
                  ->where('id',$param['pfid'])
                  ->field('content_cn,create_time')
                  ->find();
        $list['time'] = date("Y-m-d", strtotime($list['create_time']));

        return $list;
    }
}