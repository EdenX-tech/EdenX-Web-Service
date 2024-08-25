<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class KnowledgeBase extends Model
{
//    获取知识库列表
    public function knowledgeList() {
        $page =input('page')?input('page'):1;
        $pageSize =input('pageSize')?input('pageSize'):20;
        $list = db::name('knowledge_base')->alias('kb')
                ->join('user u','u.id = kb.user_id')
                ->field('u.username, u.userpic, u.address, kb.id as kb_id, kb.title_en, kb.content_en, kb.code_snippet, kb.ai_status')
                ->where('kb.is_delete',1)
                ->where('kb.status',0)
                ->order('kb.create_time desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
        foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['kb_count'] = $this->getKnowledgeAnswerCount($value['kb_id']);
        }
        $count = db::name('knowledge_base')->alias('kb')->join('user u','u.id = kb.user_id')->where('kb.is_delete',1)->count();
        $list['totalPage'] = ceil($count/$pageSize);
        $list['kb_count'] = $this->getKnowledgeCount();
        $list['ka_count'] = $this->getKnowledgeAnswerCount();

        return $list;
    }

//    获取置顶
    public function getTop() {
        $list = db::name('knowledge_base')->alias('kb')
            ->join('user u','u.id = kb.user_id')
            ->field('u.username, u.userpic, u.address, kb.id as kb_id, kb.title_en, kb.content_en, kb.code_snippet')
            ->where('kb.is_delete',1)
            ->where('kb.status',1)
            ->select();
        foreach ($list as $key => $value) {
            $list[$key]['kb_count'] = $this->getKnowledgeAnswerCount($value['kb_id']);
            $list[$key]['code_snippet'] = substr($value['code_snippet'], 1, 400) . '......';
        }
        return $list;
    }
// 发布问题
    public function createKnowledge() {
        $user_id = request()->userId;

        $params = request()->param();

        $create_info = $this->create([
            'title_en' =>  $params['title'],
            'user_id' =>  $user_id,
            'content_en' =>  $params['content'],
            'code_snippet' => $params['code_snippet'],
        ]);

        return $create_info;
    }

//    当前总题数
    public function getKnowledgeCount($k_id = '') {
        if ($k_id) {
            $count = $this->where('is_delete',1)->where('k_id', $k_id)->count();
        } else {
            $count = $this->where('is_delete',1)->count();
        }
        return $count;
    }

//    参与解题人数
    public function getKnowledgeAnswerCount($k_id = '') {
        if ($k_id) {
            $count = (new KnowledgeAnswer())->where('is_delete', 1)->where('k_id', $k_id)->count();
        } else {
            $count = (new KnowledgeAnswer())->where('is_delete', 1)->count();
        }
        return $count;
    }

//    我发布的问题
    public function myselfKnowledgeList() {
        $param = request()->param();
        $userId = request()->userId;
        $page =input('page')?input('page'):1;
        $pageSize =input('pageSize')?input('pageSize'):10;

        $list = $this->where(['user_id' => $userId, 'is_delete' => 1])
                ->field('id, title_en, content_en, create_time')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();

        foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['ka_count'] = $this->getKnowledgeAnswerCount($value['id']);
        }

        return $list;
    }

//    问题详情
    public function knowledgeDetail() {
        $params = request()->param();
        $userId = request()->userId;

        $list = $this->alias('kb')->join('user u','kb.user_id = u.id')->where('kb.id',$params['k_id'])->field('u.username,u.userpic,u.address,kb.title_en,kb.content_en,kb.code_snippet,kb.create_time,kb.ai_status')->find();
        $list['kb_count'] = $this->getKnowledgeAnswerCount($params['k_id']);
        return $list;
    }

}