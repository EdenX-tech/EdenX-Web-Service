<?php

namespace app\common\model;
use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;


class ByzApi extends Model
{
    public function login() {
        $url = config('bzyapi.base_url').'token';
        $username = config('bzyapi.username');
        $password = config('bzyapi.password');

        $header = array();
        $data = [
            'username' => $username,
            'password' => $password,
            'grant_type' => 'password'
        ];

        $data = http_post($url,$header,$data);
        $dataJson = json_decode($data,true);

        $token = $this->CreateSaveToken($dataJson,$username);

        return $token;
    }


    public function CreateSaveToken($arr,$username){
        $token = $arr['access_token'];
        // 登录过期时间
        $expire = config('bzyapi.expire');
        // 保存到缓存中
        if (!Cache::set($username,$arr,$expire)) TApiException();
        // 返回token
        return $token;
    }

//  获取任务组id
    public function getTaskGroup($access_token){
        $url = config('bzyapi.base_url').'api/TaskGroup';
        $header = array("Authorization:bearer $access_token");

        $data = curl_get($url, $header);
        return $data;
    }

//  获取任务id
    public function getTask($access_token, $taskGroupId){
        $url = config('bzyapi.base_url').'api/Task?taskGroupId='.$taskGroupId;
        $header = array("Authorization:bearer $access_token");

        $data = curl_get($url, $header);
        return $data;
    }
//    获取任务内容
    public function getAllata($access_token, $task_id, $offset=0, $size=10){
        $url = config('bzyapi.base_url').'api/alldata/GetDataOfTaskByOffset?taskId='.$task_id.'&offset='.$offset.'&size='.$size ;
        $header = array("Authorization:bearer $access_token");

        $data = curl_get($url, $header);
        return $data;
    }
}