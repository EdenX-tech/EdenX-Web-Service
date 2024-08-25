<?php

namespace app\common\model;

use think\Model;

class PostTwitterImg extends Model
{
    //
    public function uploadMore(){
        $param = request()->param();

        $image = $this->upload(request()->userId,'imglist');
        // if (count($image) > 3) {
        for ($i=0; $i < count($image); $i++) {
            $image[$i]['url'] = getFileUrl($image[$i]['url']);
        }
        // }
        // for ($i=0; $i < count($image); $i++) {
        //     $image[$i]['url'] = getFileUrl($image[$i]['url']);
        // }
        return $image;
    }

    public function upload($coin_id = '', $post_twitter_id = '',$imgUrl = '')
    {
        $rulData = upload_get_img($imgUrl);
//        dump($resss);die;
        //        dump($imgUrl);die;
        // 单文件上传
//        $file = \app\common\controller\FileController::UploadEventByUrl($imgUrl);

        //        $img_url = getFileUrl($file);

        $res = self::create([
            'post_twitter_id' => $post_twitter_id,
            'img_url' => config('app.app_host').$rulData,
            'coin_id' => $coin_id
        ]);
        return $res;
    }

    // 图片是否存在
    public function isImageExist($id,$userid){
        return $this->where('user_id',$userid)->find($id);
    }
}
