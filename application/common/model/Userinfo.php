<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use app\common\controller\ALiSendMail;
use think\Db;
// use app\common\controller\AliSMSController;

class Userinfo extends Model {
    //    我发布的问题
    public function myselfKnowledgeList() {
        $userId = request()->userId;
        $page = input('page')?input('page'):1;
        $pageSize = input('pageSize')?input('pageSize'):10;

        $list = (new KnowledgeBase())->where(['user_id' => $userId, 'is_delete' => 1])
            ->field('id, title_en, content_en, create_time')
            ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
            ->toArray();

        foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['ka_count'] = (new KnowledgeBase())->getKnowledgeAnswerCount($value['id']);
        }

        $count = (new KnowledgeBase())->where(['user_id' => $userId, 'is_delete' => 1])->count();
        $list['totalPage'] = ceil($count/$pageSize);
        return $list;
    }

//    我发布的回答
    public function myselfKnowledgeAnswerList() {
        $userId = request()->userId;
        $page = input('page')?input('page'):1;
        $pageSize = input('pageSize')?input('pageSize'):10;

        $list = (new KnowledgeAnswer())->alias('ka')->join('knowledge_base kb', 'ka.k_id = kb.id')->where(['ka.user_id' => $userId, 'ka.is_delete' => 1])
            ->field('kb.id, kb.title_en, ka.content, ka.create_time')
            ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
            ->toArray();
        foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['ka_count'] = (new KnowledgeBase())->getKnowledgeAnswerCount($value['id']);
        }

        $count = (new KnowledgeAnswer())->alias('ka')->join('knowledge_base kb', 'ka.k_id = kb.id')->where(['ka.user_id' => $userId, 'ka.is_delete' => 1])->count();
        $list['totalPage'] = ceil($count/$pageSize);
        return $list;
    }

//    个人信息
    public function getUserInfo() {
        $params = request()->param();
        $userId = request()->userId;
        $list = (new User())->where('id', $userId)->field('userpic, username, award')->find();
        $list['kb'] = $this->konwledgeCount($userId);
        $list['ka'] = $this->knowledgeAnswer($userId);
        $list['user_award_count'] =  (new UserAward())->where('user_id', $userId)->count();
        $list['user_post_count'] =  (new EnPostDepth())->where('user_id', $userId)->where('is_delete', 0)->count();
        return $list;
    }

    public function konwledgeCount($user_id) {
        return (new KnowledgeBase())->where('user_id', $user_id)->where('is_delete', 1)->count();
    }

    public function knowledgeAnswer($user_id) {
        return (new KnowledgeAnswer())->where('user_id', $user_id)->where('is_delete', 1)->count();
    }
}