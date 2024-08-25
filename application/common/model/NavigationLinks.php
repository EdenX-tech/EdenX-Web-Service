<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class NavigationLinks extends Model
{
    public function linksList() {
        $param = request()->param();
        $list = $this->alias('nl')->join('navigation na','nl.na_id = na.id')->where('na.is_delete',1)->where('nl.is_delete', 1)->field('nl.na_id, nl.title, nl.url')->order('nl.sort desc')->select();
        return $list;
    }

}