<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use app\common\controller\ALiSendMail;
use think\Db;
// use app\common\controller\AliSMSController;

class KnowledgeAnswer extends Model
{
    public function knowledgeAnswerList() {
        $k_id = request()->param('k_id');

        $list = db::name('knowledge_answer')->alias('ka')->join('user u','u.id = ka.user_id')->where([
            'ka.k_id' => $k_id,
            'ka.is_delete' => 1
        ])->field('u.username, u.id as user_id, ka.id as ka_id, ka.content, ka.create_time, u.userpic, u.address')->select();

        return $list;
    }

    public function createKnowledgeAnswer() {
        $user_id = request()->userId;

        $params = request()->param();

        $create_info = $this->create([
            'user_id' =>  $user_id,
            'content' =>  $params['content'],
            'k_id' => $params['k_id'],
        ]);

        return $create_info;
    }
}