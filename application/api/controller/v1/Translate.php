<?php
namespace app\api\controller\v1;

use app\common\controller\BaseController;
use app\common\controller\ALiTransController;
use app\common\controller\BaiduTransController;
use think\Request;

class Translate extends BaseController
{
    // 翻译
    public function getTranslateContent($translate_content){

//        $params = request()->param();
        $params['translate_content'] = $translate_content;

        if (!isset($params['translate_content']) || !$params['translate_content']) {
            TApiException('parameter error', 40002);
        }
        $translate_res = '';

        // google 翻译
        // $google_translate = $this->gtranslate($params['translate_content']);
        // if ($google_translate === false) {
        // ali 翻译
        $ali_translate = ALiTransController::main($params['translate_content']);

//        dump($ali_translate['Code']);die;
        if ($ali_translate['Code'] != 200) {
            // baidu 翻译
            $baidu_translate = BaiduTransController::translate($params['translate_content']);
            ;
            if (isset($baidu_translate['error_code'])) {
                TApiException('翻译失败, 请重试', 40002);
            }else{
                trace('baidu translate', 'info');
                $translate_res = $baidu_translate['trans_result'][0]['dst'];

            }
        }else{
            trace('ali translate', 'info');
            $translate_res = $ali_translate['Data']['Translated'];
        }
        // }else{
        // 	trace('google translate', 'info');
        // 	$translate_res = $google_translate;
        // }

        return $translate_res;
    }

    // google 翻译
    public function gtranslate($text, $to='zh-CN'){
        $entext = urlencode($text);
        $url = 'https://translate.google.cn/translate_a/single?client=gtx&dt=t&ie=UTF-8&oe=UTF-8&sl=auto&tl='.$to.'&q='.$entext;
        set_time_limit(0);
        $ch = curl_init();
        $useragent = array(
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)',
            'Mozilla/5.0 (Windows; U; Windows NT 5.2) Gecko/2008070208 Firefox/3.0.1',
            'Opera/9.27 (Windows NT 5.2; U; zh-cn)',
            'Opera/8.0 (Macintosh; PPC Mac OS X; U; en)',
            'Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13 ',
            'Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Version/3.1 Safari/525.13'
        );
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent[array_rand($useragent)]);

        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_MAXREDIRS,20);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);

        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // var_dump($httpCode);
        // var_dump($result);

        $result = json_decode($result, true);

        if(!empty($result)){
            foreach($result[0] as $k){
                $v[] = $k[0];
            }
            return implode(" ", $v);
        }
        return false;
    }
}