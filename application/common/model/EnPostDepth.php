<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;

class EnPostDepth extends Model
{
    public function postDepthList(){
        $page =input('page') ? input('page') : 1;
        $pageSize =input('pageSize') ? input('pageSize') : 10;
        $subcategory_id = input('subcategory_id') ? input('subcategory_id') : 1;

        $where = [
            'pd.is_delete' => 0,
            'pd.category_id' => 2,
            'pd.subcategory_id' => $subcategory_id
        ];

        $list = db::name('en_post_depth')
            ->alias('pd')
            ->join('user u','pd.user_id = u.id')
            ->where($where)
            ->field('u.id as u_id, u.username, u.userpic as u_userpic, u.address, pd.token as post_token, pd.titlepic, pd.title, pd.desc, pd.create_time')
            ->order('pd.create_time desc')
            ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
            ->toArray();

        return $list;
    }

    public function depthDetail(){
        $params = request()->param();

        $list = db::name('en_post_depth')->alias('pd')
                ->join('user u','pd.user_id = u.id')
                ->where('pd.token',$params['post_token'])
                ->field('u.id as u_id, u.address, u.username, u.userpic as u_userpic, pd.id as post_id, pd.token as post_token, pd.titlepic, pd.title, pd.desc, pd.content, pd.category_id, pd.subcategory_id, pd.create_time')
                ->find();

        return $list;
    }

    public function getRightDepth(){
        $param = request()->param();

        $list = db::name('en_post_depth')->alias('pd')->join('user u','pd.user_id = u.id')
                //->where('pd.user_id',$param['user_id'])
                ->where('pd.type',$param['type'])
                ->where('pd.class_id',$param['class_id'])
                ->where('pd.is_delete',0)->field('u.id as u_id, u.username, u.userpic as u_userpic, pd.*')->order('pd.create_time asc')->select();


        $list['count'] = db::name('en_post_depth')->alias('pd')->join('user u','pd.user_id = u.id')
                  //->where('pd.user_id',$param['user_id'])
                  ->where('pd.type',$param['type'])
                  ->where('pd.class_id',$param['class_id'])
                  ->where('pd.is_delete',0)
                  ->count();
        return $list;
    }

    public function createPost() {
        $param = request()->param();
        $user_id = request()->userId;

        $post = $this->create([
            'title'       => $param['title'],
            'titlepic'    => $param['titlepic'],
            'desc'        => $param['desc'],
            'content'     => $param['content'],
            'user_id'     => $user_id,
            'category_id' => $param['category_id'],
            'subcategory_id' => $param['subcategory_id'],
            'token'       => create_code('Depth')
        ]);

        return $post ? true : TApiException();
    }

    public function getCategoryList() {
        return (new Category()) -> select();
    }

    public function getSubcategoryList() {
        return (new Subcategory()) -> where('category_id', 2) -> select();
    }

    public function userPostList(){
        $user_id = request()->userId;

        $page =input('page') ? input('page') : 1;
        $pageSize =input('pageSize') ? input('pageSize') : 10;

        $where = [
            'pd.is_delete' => 0,
            'pd.user_id' => $user_id
        ];

        $list = db::name('en_post_depth')
            ->alias('pd')
            ->join('user u','pd.user_id = u.id')
            ->where($where)
            ->field('u.id as u_id, u.username, u.userpic as u_userpic, u.address, pd.id as post_id, pd.token as post_token, pd.titlepic, pd.title, pd.desc, pd.create_time')
            ->order('pd.create_time desc')
            ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
            ->toArray();

        return $list;
    }

    public function edit(){
        $params = request()->param();

        $post = $this -> allowField(true)->save($params,['id' => $params['post_id']]);
    
        if (!$post) TApiException();

        return true;
    }

    public function delete(){
        $params = request()->param();
        $post = $this->get($params['post_id']);

        $post -> is_delete = 1;

        $post -> save();

        return $post ? true : false;
    }
}