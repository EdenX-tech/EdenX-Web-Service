<?php

namespace app\common\model;

use app\common\controller\FileController;
use think\Model;
use think\model\Collection;

class Image extends Model
{
    //
    public function uploadMore($user_id = 1){
        $image = $this->upload($user_id,'file');

        if ($image instanceof Collection && count($image) >=1){
            for ($i=0; $i < count($image); $i++) {
                $image[$i]['url'] = $image[$i]['url'];
            }
            return $image;
        }

        $image['url'] = $image['url'];
        return $image;
    }

    public function upload($userid = '',$field = ''){
        $files = request()->file($field);
        if (is_array($files)){
            //            多图上传
            $arr = [];
            foreach ($files as $file){
                $res = FileController::AwsUploadEvent($file);
                if ($res['status']) {
                    $arr[] = [
                        'url' => $res['data'],
                        'user_id' => $userid
                    ];
                }
            }
            return $arr ? $this->saveAll($arr) : TApiException('上传失败',10000,200);
        }
        if (!$files) TApiException('请选择要上传的照片',10000,200);
        //        单文件上传
        $file = FileController::AwsUploadEvent($files);
        // dump($file);die;
        if (!$file['status']) TApiException($file['data'],10000,200);
//        dump($file['data']);die;
        return self::create([
            'url' => $file['data'],
            'user_id' => $userid
        ]);
    }
    // 图片是否存在
    public function isImageExist($id,$userid = ''){
        if (!$userid) {
            return $this->where('id',$id)->find($id);
        }
        return $this->where('user_id',$userid)->find($id);
    }
}

