<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
// use app\common\controller\AliSMSController;

class TwitterUser extends Model
{
    public function getAll(){
        $list = db::name('coin')->where('is_delete',0)->field('id, twitter_url, symbol, title')->select();

        return $list ?? [];
    }

    public function followMail($coin_id){
        $mail_array = db::name('follow_coin')->alias('fc')->join('user u','fc.user_id = u.id')->where(['fc.coin_id'=>$coin_id,'fc.is_delete'=>0])->field('u.email')->select();
        $mail_str = $this->arr_to_str($mail_array);
        return $mail_str;
    }

    public function arr_to_str($arr) {
        $t ='' ;
        $temp = array();
        foreach ($arr as $v) {
            $v = join(",",$v); // 可以用implode将一维数组转换为用逗号连接的字符串，join是别名
            $temp[] = $v;
        }
        foreach ($temp as $v) {
            $t.=$v.",";
        }
        $t = substr($t, 0, -1); // 利用字符串截取函数消除最后一个逗号
        return $t;
    }
}