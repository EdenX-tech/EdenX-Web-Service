<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
/**
 *
 */
class EnBanner extends Model
{
    public function bannerList(){
        $list = $this->where('is_delete',0)->order('sort desc')->select();
        return $list;
    }
}