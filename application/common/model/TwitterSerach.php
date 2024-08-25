<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
// use app\common\controller\AliSMSController;

class TwitterSerach extends Model
{
    public function serachList(){
        $key=input('post.key') ? input('post.key') : '';
        $page =input('page')?input('page'):1;
        $pageSize =input('limit')?input('limit'):10;
        $sort_by = input('sort_by') ? input('sort_by') : '';
        $sort_order = input('sort_order') ? input('sort_order') : 'desc';

        $list=Db::name('twitter_serach')->alias('ts')
                ->join('twitter_key tk','ts.key_id = tk.id','left')
                ->field('ts.*,tk.key')
                ->where('ts.status',0)
                ->where('ts.symbol|tk.key','like',"%".$key."%")
                ->where('tk.key','like',"%".$sort_by."%")
                ->order('ts.created_at',$sort_order)
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
        return $list;
    }
}