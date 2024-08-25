<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
/**
 *
 */
class Project extends Model
{
    // 全部
    public function projectList(){

        $page =input('page')?input('page'):1;
        $pageSize =input('pageSize')?input('pageSize'):10;
        $key = input('project_name');
      
        $list = $this
                ->where('is_delete', 0)
                ->when($key, function ($q) use ($key) {
                    $q->whereLike('title|symbol', '%'.$key.'%');
                })
                ->field('id as project_id, title, symbol, logo, forum_url, medium_url, twitter_url, remark, website')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
          
        return $list;
    }


    public function createProject(){
        $param = request()->param();
        $Project = $this->create([
            'title' => $param['title'],
            'symbol' => $param['symbol'],
            'logo' => $param['logo'],
            'website' => $param['website'],
            'forum_url' => $param['forum_url'],
            'medium_url' => $param['medium_url'],
            'twitter_url' => $param['twitter_url'],
            'remark' => $param['remark'],
            'en_remark' => $param['en_remark'],
        ]);

        return $Project ? true : false;
    }

    public function editProject(){
        $params = request()->param();

        $edit = $this->allowField(true)->save($params, ['id' => $params['project_id']]);

        return $edit ? true : false;
    }

    public function deleteProject(){
        $param = request()->param();
        $project = $this->get($param['project_id']);
        $project->is_delete = $param['is_delete'];
        $project->save();
        return true;
    }

}