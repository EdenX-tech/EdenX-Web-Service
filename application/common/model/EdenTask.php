<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
use app\lib\TwitterAuth;

class EdenTask extends Model
{

    // 定义关联关系
    public function taskFollow()
    {
        // hasOne('关联模型名', '外键名', '主键名', [], 'join类型')
        return $this->hasOne('TaskFollow', 'eden_task_id', 'id');
    }

    public function taskShare()
    {
        // hasOne('关联模型名', '外键名', '主键名', [], 'join类型')
        return $this->hasOne('TaskShare', 'eden_task_id', 'id');
    }

    public function taskQuestion()
    {
        // hasOne('关联模型名', '外键名', '主键名', [], 'join类型')
        return $this->hasOne('TaskQuestion', 'eden_task_id', 'id');
    }
    
    public function taskProject()
    {
        // hasOne('关联模型名', '外键名', '主键名', [], 'join类型')
        return $this->hasOne('Project', 'project_id');
    }
    
    public function getLeransListField(int $lg) {
        $list = array();

        switch ($lg)
        {
            case 1:
                $list = 'id, title, experience_count, base_node_id, reward_count, level, desc, titlepic, rules';
                break;
            case 2:
                $list = 'id, en_title as `title`, experience_count, base_node_id, reward_count, level, en_desc as `desc`, titlepic, en_rules as `rules`';
                break;
            default:
                $list = 'id, title, experience_count, base_node_id, reward_count, level, desc, titlepic, rules';
        }

        return $list;
    }


    public function getBaseQuestionField(int $lg) {
        $list = array();

        switch ($lg)
        {
            case 1:
                $list[0] = 'id, title, analyse, bqt_id';
                $list[1] = 'id, content, is_correct';
                break;
            case 2:
                $list[0] = 'id, bqt_id, en_analyse as analyse, en_title as title';
                $list[1] = 'id, en_topic as content, is_correct';
                break;
            default:
                $list[0] = 'id, bqt_id, en_analyse as analyse, en_title as title';
                $list[1] = 'id, en_topic as content, is_correct';
        }

        return $list;
    }
    
    public function taskVideoList() {
        $params = request()->param();
        $user_id = $params['user_id'] ?? 0;
        
        $list['base']     = $this->getVideoBaseList($user_id);
        $list['advanced'] = $this->getVideoAdvancedList($user_id);
        $list['practice'] = $this->getVideoPracticeList($user_id);

        return $list;
    }

    public function taskPostList() {
        $params = request()->param();
        $user_id = $params['user_id'] ?? 0;
        $list['technology']     = $this->getPostTechnologyyList($user_id);
        $list['ecology'] = $this->getPostEcologyList($user_id);

        return $list;
    }

    public function taskAlllist() {
        $page =1;
        $pageSize =30;
        $task_attribute = input('task_attribute');
        $user_id = $params['user_id'] ?? 0;
        
        $list = $this
            ->alias('et')
            ->leftJoin('project p', 'et.project_id = p.id')
            ->where(['task_attribute_id' => $task_attribute, 'is_open' => 1])
            ->field('et.id as task_id, et.task_title, et.task_desc, et.titlepic, et.task_award_amount, et.task_count, et.task_is_award, p.title, p.logo')
            ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
            ->toArray();
        
        foreach ($list['data'] as $key=> $value) {
            $i = 0;
            $list['data'][$key]['user_task_end_count'] = $this->userTaskEndCount($user_id, $value['task_id']);
            if ((new TaskFollow())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskQuestion())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskShare())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            $list['data'][$key]['task_end_count'] = $i;
            
            $list['data'][$key]['task_count'] = $value['task_count'] * 16;
        }
        return $list;
    }

    public function taskDeatil()
    {
        $params = request()->param();
        $user_id = $params['user_id'] ?? 0;
    
        $list = $this
            ->where('id', $params['task_id'])
            ->with([
                'taskFollow' => function ($q) {
                    $q->field('id,eden_task_id,follow_content'); // 你可以选择要查询的字段
                },
                'taskQuestion' => function ($q) {
                    $q->field('id,eden_task_id,base_question_id'); // 你可以选择要查询的字段
                },
                'taskShare' => function ($q) {
                    $q->field('id,eden_task_id,share_content'); // 你可以选择要查询的字段
                }
            ])
            ->find();

       if ($list['task_is_award'] != 1) {
           $this->where('id', $params['task_id'])->setInc('task_count');
       }
        if ($list['task_follow']) {
            $list['task_follow']['status'] = $user_id ? $this->tasksUserOrderStatus($user_id, $list['id'], 'task_follow') : ['task_status'=> 0, 'is_open'=>0];
        } else {
            unset($list['task_follow']);
        }
        
        if ($list['task_question']['base_question_id']) {
            $list['task_question']['status'] = $user_id ? $this->tasksUserOrderStatus($user_id, $list['id'], 'task_question') : ['task_status'=> 0, 'is_open'=>0];
            $list['task_question']['task_question_list'] = $this->questionList($list['task_question']['base_question_id'], request()->lg);
        } else {
            unset($list['task_question']);
        }
        
        // if ($list['task_question']) $list['task_question']['task_question_list'] = $this->questionList($list['task_question']['base_question_id'], request()->lg);
        
        if ($list['task_share']) {
            $list['task_share']['status'] = $user_id ? $this->tasksUserOrderStatus($user_id, $list['id'], 'task_share') : ['task_status'=> 0, 'is_open'=>0];
        }  else {
            unset($list['task_share']);
        }
        
        $list['post_token'] = (new EnPostDepth())->where('id', $list['task_post_id'])->value('token');
        $list['user_task_status'] = $this->tasksUserStatus($user_id, $params['task_id']);
        $list['task_type'] = db::name('task_attribute')->where('id', $list['task_attribute_id'])->value('task_class_id');
        return $list;
    }

    public function questionList($base_question_id, $lg = 2) {
        $field = $this->getBaseQuestionField($lg);
        
        $list = db::name('base_question')
            ->whereIn('id', $base_question_id)
            ->field($field[0])
            ->select();
        
        $field = $this->getBaseQuestionField(request()->lg);
        foreach ($list as $key=>$value) {
            $bqt_id = explode(",", $value['bqt_id']);
            if (count($bqt_id) > 0) {
                for ($i=0; $i<count($bqt_id); $i++) {
                    $list[$key]['qn'][] = db::name('base_question_topic')->where('id', $bqt_id[$i])->field($field[1])->find();
                }
            }
            unset($value['bqt_id']);
        }
        return $list;
    }

    public function startTasks() {
        $params = request()->param();
        $user_id = request()->userId;

        $user_tasks_status = $this->tasksUserStatus($user_id, $params['task_id']);

        if ($user_tasks_status == 1 || $user_tasks_status == 2) TApiException("当前任务已经开启");

        $list = $this
            ->where('id', $params['task_id'])
            ->with([
                'taskFollow' => function ($q) {
                    $q->field('id,eden_task_id,follow_content'); // 你可以选择要查询的字段
                },
                'taskQuestion' => function ($q) {
                    $q->field('id,eden_task_id,base_question_id'); // 你可以选择要查询的字段
                },
                'taskShare' => function ($q) {
                    $q->field('id,eden_task_id,share_content'); // 你可以选择要查询的字段
                }
            ])
            ->find();

        if ($list['task_follow']) $this->createTaskFollow($list['id'], 'task_follow', $list['task_follow']['id'], $user_id);
        if ($list['task_question']) $this->createTaskFollow($list['id'], 'task_question', $list['task_question']['id'], $user_id);
        if ($list['task_share']) $this->createTaskFollow($list['id'], 'task_share', $list['task_question']['id'], $user_id);

        return true;
    }

    public function underwayTask() {
        $params = request()->param();
        $user_id = request()->userId;
        
        $user_tasks_status = $this->tasksUserStatus($user_id, $params['task_id']);
 
        if ($user_tasks_status == 1) TApiException("任务已完成");
        if ($user_tasks_status == 0) TApiException("请先接受任务");
        
        if ($params['task_type'] == 'task_follow') {
            $this -> checkUnderwayFollow($user_id) ??  TApiException("请先完成任务");
            $this -> underwayTasks($user_id, $params['task_id'], 'task_follow');
        }

        if ($params['task_type'] == 'task_question') {
            $this -> checkUnderwayQuestion($params['content'],$params['task_id']) ??  TApiException("请先完成任务");
            $this -> underwayTasks($user_id, $params['task_id'], 'task_question');
        }

        if ($params['task_type'] == 'task_share') {
            $this -> checkUnderwayShare() ??  TApiException("请先完成任务");
            $this -> underwayTasks($user_id, $params['task_id'], 'task_share');
        }

        $new_task_type = (new TaskUserOrder())->where(['user_id' => $user_id, 'task_id' => $params['task_id'], 'is_open' => 0])->field('task_type')->find();

        if ($new_task_type) $this->openTasks($user_id, $params['task_id'], $new_task_type['task_type']);

        return true;
    }

    public function underwayTasks($user_id, $task_id, $task_type) {
        (new TaskUserOrder())->where(['user_id'=>$user_id, 'task_id'=>$task_id, 'task_type'=>$task_type])->update(['task_status' => 1]);
    }

    public function openTasks($user_id, $task_id, $task_type) {
        (new TaskUserOrder())->where(['user_id'=>$user_id, 'task_id'=>$task_id, 'task_type'=>$task_type])->update(['is_open' => 1]);

    }

    public function checkUnderwayFollow(int $user_id) {
        return true;
        $status = TwitterAuth::friendsShow($user_id);
        return $status ? true : TApiException('验证失败');
        
    }

    public function checkUnderwayQuestion($task_question_content, $task_id) {
//        $task_question_content = [
//            [
//                'question_id'=>
//                'question_topic_id'=>
//            ],
//            [
//            'question_id'=>
//                'question_topic_id'=>
//            ],
//        ];
//        当前题目是否一致
        $task_question = [];
        foreach ($task_question_content as $key => $value) {
            $base_question_id = $value['question_id'];
            $task_question = (new TaskQuestion())->where('eden_task_id', $task_id)->where("FIND_IN_SET($base_question_id, base_question_id)")->find();
//            FIND_IN_SET($value['question_id'], base_question_id)
            if (!$task_question) TApiException('参数有误');

//            当前题目是否正确
            $is_correct = db::name('base_question_topic')->where('id', $value['is_correct_id'])->field('is_correct')->find();
            if (!$is_correct['is_correct']) TApiException('参数有误02');
        }

        $count_question = explode(",", $task_question['base_question_id']);

        if (count($count_question) != count($task_question_content)) TApiException('参数有误03');
        return true;
    }

    public function checkUnderwayShare() {
        return true;
    }

    public function endTask() {
        $params = request()->param();
        $user_id = request()->userId;

        self::taskCheckGather($user_id, $params['task_id']);

        return true;
    }

    // 领取奖励
    public function ReceiveAward()
    {
        $params = request()->param();
        $user_id = request()->userId;

        self::taskCheckGather($user_id, $params['task_id']);

        $task_info = $this->where('id', $params['task_id'])->field('id, code, is_open, task_award_amount, task_is_award, task_count')->find();

        $award_count = (new TaskUserLog())->where('task_id', $params['task_id'])->count();

        if ($task_info['is_open'] == 0) TApiException("任务已关闭");
        if ($task_info['task_count'] < $award_count) {
            $task_info->is_open = 0;
            $task_info->task_is_award = 0;
            $task_info->is_award_amount = 0;
            $task_info->task_count = 0;
            $task_info->save();

            TApiException("超出当前任务领取奖励上限");
        }

        return $task_info['code'];
    }

    // 成功领取后回调
    public function callBackReceiveAward()
    {
        $params = request()->param();
        $user_id = request()->userId;

        self::taskCheckGather($user_id, $params['task_id']);

        $task_info = $this->where('id', $params['task_id'])->field('id, code, is_open, task_award_amount, task_is_award, task_count')->find();

        (new TaskUserLog())->create([
            'user_id' => $user_id,
            'task_id' => $task_info->id,
            'task_award_amount' => $task_info->task_award_amount
        ]);

        $award_count = (new TaskUserLog())->where('task_id', $params['task_id'])->count();

        if ($task_info['task_count'] <= $award_count) {
            $task_info->is_open = 0;
            $task_info->task_is_award = 0;
            $task_info->is_award_amount = 0;
            $task_info->task_count = 0;
            $task_info->save();
        }

        return true;
    }

//    任务验证集
    protected function taskCheckGather($user_id, $task_id) {
        $is_log = $this->tasksUserStatus($user_id, $task_id);

        if ($is_log == 1)  TApiException("已领取过奖励");

        $taskinfo = (new TaskUserOrder())->where(['task_id'=>$task_id, 'user_id'=>$user_id, 'task_status'=>0])->find();
        if ($taskinfo) TApiException("请先完成任务");
    }
// 1.领取奖励 2。已接受任务未完成 3.一接受任务已完成 0。未接受任务
    public function tasksUserStatus($user_id, $task_id)
    {
        $is_over = (new TaskUserLog())->where([
            'user_id' => $user_id,
            'task_id' => $task_id])
            ->find();
        if ($is_over) {
            $user_task_status = 1;
        } else {
            $list = (new TaskUserOrder())->where(['user_id' => $user_id, 'task_id' => $task_id])->find();
            if (!$list) {
                $user_task_status = 0;
            } else {
                $list = (new TaskUserOrder())->where(['user_id' => $user_id, 'task_id' => $task_id, 'task_status' => 0])->find();

                if (!$list) {
                    $user_task_status = 3;
                } else {
                    $user_task_status = 2;
                }
            }

        }
        return $user_task_status;
    }
    
    //当前完成状态
    public function userTaskEndCount($user_id, $task_id)
    {
        $user_task_end_count = (new TaskUserOrder())->where(['user_id' => $user_id, 'task_id' => $task_id, 'task_status'=>1])->count();
        return $user_task_end_count;
    }

    public function tasksUserOrderStatus(int $user_id, int $task_id, $tasks_type) {
        $tasks_status = (new TaskUserOrder())->where(['user_id' => $user_id, 'task_id' => $task_id, 'task_type' => $tasks_type])->field('task_status, is_open')->find();

        return $tasks_status ? $tasks_status : ['task_status'=> 0, 'is_open'=>0];;

    }

    public function getVideoBaseList($user_id) {
        $list = $this
            ->alias('et')
            ->leftJoin('project p', 'et.project_id = p.id')
            ->where(['et.task_attribute_id' => 1, 'et.is_open' => 1])
            ->field('et.id as task_id, et.task_title, et.task_desc, et.titlepic, et.task_award_amount, et.task_count, et.task_is_award, p.title, p.logo')
            ->limit(10)
            ->select();
        foreach ($list as $key=> $value) {
            $i = 0;
            $list[$key]['user_task_end_count'] = $this->userTaskEndCount($user_id, $value['task_id']);
            // 'taskFollow' => function ($q) {
            //         $q->field('id,eden_task_id,follow_content'); // 你可以选择要查询的字段
            //     },
            //     'taskQuestion' => function ($q) {
            //         $q->field('id,eden_task_id,base_question_id'); // 你可以选择要查询的字段
            //     },
            //     'taskShare' => function ($q) {
            //         $q->field('id,eden_task_id,share_content'); // 你可以选择要查询的字段
            //     }
            if ((new TaskFollow())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskQuestion())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskShare())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            $list[$key]['task_end_count'] = $i;
            
            $list[$key]['task_count'] = $value['task_count'] * 16;
        }
        return $list;
    }

    public function getVideoAdvancedList($user_id) {
        $list = $this
            ->alias('et')
            ->leftJoin('project p', 'et.project_id = p.id')
            ->where(['et.task_attribute_id' => 2, 'et.is_open' => 1])
            ->field('et.id as task_id, et.task_title, et.task_desc, et.titlepic, et.task_award_amount, et.task_count, et.task_is_award, p.title, p.logo')
            ->limit(10)
            ->select();
        foreach ($list as $key=> $value) {
            $i = 0;
            $list[$key]['user_task_end_count'] = $this->userTaskEndCount($user_id, $value['task_id']);
            if ((new TaskFollow())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskQuestion())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskShare())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            $list[$key]['task_end_count'] = $i;
            
            $list[$key]['task_count'] = $value['task_count'] * 16;
        }
        return $list;

    }

    public function getVideoPracticeList($user_id) {
        $list = $this
            ->alias('et')
            ->leftJoin('project p', 'et.project_id = p.id')
            ->where(['et.task_attribute_id' => 3, 'et.is_open' => 1])
            ->field('et.id as task_id, et.task_title, et.task_desc, et.titlepic, et.task_award_amount, et.task_count, et.task_is_award, p.title, p.logo')
            ->limit(10)
            ->select();
        foreach ($list as $key=> $value) {
            $i = 0;
            $list[$key]['user_task_end_count'] = $this->userTaskEndCount($user_id, $value['task_id']);
            if ((new TaskFollow())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskQuestion())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskShare())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            $list[$key]['task_end_count'] = $i;
            
            $list[$key]['task_count'] = $value['task_count'] * 16;
        }
        return $list;
    }

    public function getPostTechnologyyList($user_id) {
        $list = $this
            ->alias('et')
            ->leftJoin('project p', 'et.project_id = p.id')
            ->where(['et.task_attribute_id' => 4, 'et.is_open' => 1])
            ->field('et.id as task_id, et.task_title, et.task_desc, et.titlepic, et.task_award_amount, et.task_count, et.task_is_award, p.title, p.logo')
            ->order('et.create_time desc')
            ->limit(10)
            ->select();
        foreach ($list as $key=> $value) {
            $i = 0;
            $list[$key]['user_task_end_count'] = $this->userTaskEndCount($user_id, $value['task_id']);
            if ((new TaskFollow())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskQuestion())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskShare())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            $list[$key]['task_end_count'] = $i;
            
            $list[$key]['task_count'] = $value['task_count'] * 16;
        }
        return $list;
    }

    public function getPostEcologyList($user_id) {
        $list = $this
            ->alias('et')
            ->leftJoin('project p', 'et.project_id = p.id')
            ->where(['et.task_attribute_id' => 5, 'et.is_open' => 1])
            ->field('et.id as task_id, et.task_title, et.task_desc, et.titlepic, et.task_award_amount, et.task_count, et.task_is_award, p.title, p.logo')
            ->order('et.create_time desc')
            ->limit(10)
            ->select();
        foreach ($list as $key=> $value) {
            $i = 0;
            $list[$key]['user_task_end_count'] = $this->userTaskEndCount($user_id, $value['task_id']);
            if ((new TaskFollow())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskQuestion())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            if ((new TaskShare())->where('eden_task_id', $value['task_id'])->find()) $i ++;
            $list[$key]['task_end_count'] = $i;
            
            $list[$key]['task_count'] = $value['task_count'] * 16;
        }
        return $list;
    }

    public function createTaskFollow($task_id, $task_type, $task_data, $user_id) {
       
        (new TaskUserOrder()) -> create([
            'task_id' => $task_id,
            'task_type' => $task_type,
            'task_data_id' => $task_data,
            'user_id' => $user_id,
            'is_open' => 1
        ]);
    }
}