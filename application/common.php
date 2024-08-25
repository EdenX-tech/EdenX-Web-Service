<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 异常类输出函数
function TApiException($msg = '异常', $errorCode = 999, $code = 400){
    throw new \app\lib\exception\BaseException(['code'=>$code,'msg'=>$msg,'errorCode'=>$errorCode]);
}


function http_post($sUrl, $aHeader = array(), $aData = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $sUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
    $sResult = curl_exec($ch);
    if ($sError = curl_error($ch)) {
        die($sError);
    }
    curl_close($ch);

    return $sResult;
}

function curl_get($url,$header=array()){

//    $header = array(
//        'Accept: application/json',
//    );
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 超时设置,以秒为单位
    curl_setopt($curl, CURLOPT_TIMEOUT, 1);

    // 超时设置，以毫秒为单位
    // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);

    // 设置请求头
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    //执行命令
    $data = curl_exec($curl);

    // 显示错误信息
    if (curl_error($curl)) {
        print "Error: " . curl_error($curl);
    } else {
        // 打印返回的内容
        // var_dump($data);
        return $data;
        curl_close($curl);
    }
}

//远程路径，名称，文件后缀
function downImgRar($url,$rename,$ext){
    switch ($ext) {
        case 'jpg':    //下载图片
            $file_path = '/uploads/images/';
            break;
        case 'png':    //下载图片
            $file_path = '/uploads/images/';
            break;
        case 'pdf':    //下载PDF
            $file_path = 'uploads/pdf/';
            break;
        case 'rar':    //下载压缩包
            $file_path = 'uploads/rar/';
            break;
        case 'zip':    //下载压缩包
            $file_path = 'uploads/rar/';
            break;
        default:
            $file_path = 'uploads/files/';
            break;
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $rawdata=curl_exec ($ch);
    curl_close ($ch);
    // 使用中文文件名需要转码
    $fp = fopen($file_path.iconv('UTF-8', 'GBK', $rename).".".$ext,'w');
    fwrite($fp, $rawdata);
    fclose($fp);
    // 返回路径
    return $_SERVER['DOCUMENT_ROOT'].$file_path.$rename.".".$ext;
}


function upload_get_img($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $info = curl_exec($curl);
    curl_close($curl);

    $timeStr = time();

    $newFileName = Env::get('root_path').'public/uploads/'.$timeStr.'.png';//保存的本地地址及文件名
    $returnFileName = $timeStr.'.png';
//    dump($newFileName);die;
    $fp2 = @fopen($newFileName, "a+");

    fwrite($fp2, $info);
    fclose($fp2);
    return $returnFileName;//返回新的文件路径及文件名
}

function completions($API_KEY,$TEXT,$success = "") {
    $header = array(
        'Authorization: Bearer '.$API_KEY,
        'Content-type: application/json',
    );

    $params = json_encode(array(
        'messages'=>array($TEXT),
        //'messages'=>$TEXT,
        'model' => 'gpt-3.5-turbo',

    ));
//    dump($params);die;
    $curl = curl_init('https://api.openai.com/v1/chat/completions');
    $options = array(
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_POSTFIELDS => $params,
        CURLOPT_RETURNTRANSFER => true,
    );
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $text = "服务器连接错误,请稍后再试!";

    if (200 == $httpcode || 429 == $httpcode || 401 == $httpcode || 400 == $httpcode) {
        $json_array = json_decode($response, true);

        if (isset($json_array['choices'][0]['message']['content'])) {
            $text = str_replace("\\n", "\n", $json_array['choices'][0]['message']['content']);
            if ($success == true) {
                success();
            }
        } elseif (isset($json_array['error']['message'])) {
            $text = $json_array['error']['message'];
        } else {
            $text = "对不起，我不知道该怎么回答。";
        }
    }
    return $text;
}

function create_code($type)
{
    $code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $osn = $code[intval(date('Y')) - 2015] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    $osn = substr($osn, 8);
    $osn = $type .'-'. $osn;
    return $osn;
}

function generateRandomString($length = 5) {
     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
     $randomString = '';

     for ($i = 0; $i < $length; $i++) {
         $randomString .= $characters[rand(0, strlen($characters) - 1)];
     }

     return $randomString;
}

function insertString($originalString, $position, $insertText) {
     if ($position < 0 || $position > strlen($originalString)) {
         return $originalString;
     }
    
     $part1 = substr($originalString, 0, $position);
     $part2 = substr($originalString, $position);
    
     return $part1 . $insertText . $part2;
 }

