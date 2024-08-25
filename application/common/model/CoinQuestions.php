<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class CoinQuestions extends Model
{
    public function sendQuestion(){
        $user_id = request()->userId;

        $params = request()->param();

        $create_info = $this->create([
           'title' =>  $params['title'],
           'user_id' =>  $user_id,
           'content' =>  $params['content'],
        ]);

        return $create_info ? true : TApiException('提交失败',36000);
    }
}