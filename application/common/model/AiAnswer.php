<?php

namespace app\common\model;
use think\Model;
use think\facade\Cache;
use app\lib\exception\BaseException;
use think\Db;
use GuzzleHttp\Client;

class AiAnswer extends Model
{
    private function setOpenAi() {
        $open_api_key = config('api.open_api_key');

        $openaiClient = \Tectalic\OpenAi\Manager::build(
            new \GuzzleHttp\Client(),
            new \Tectalic\OpenAi\Authentication($open_api_key)
        );
        
        return $openaiClient;
    }

    public function answer() {

        $param = request()->param();

        $result = $this -> verify($param['kb_id']);

        if ($result) {
            // $openaiClient = self::setOpenAi();
            // $formatQuestion = $this -> aiFormat($param['title'], $param['description'], $param['code']);
            // $response = $openaiClient->completions()->create(
            //     new \Tectalic\OpenAi\Models\Completions\CreateRequest([
            //         // 'model' => 'text-davinci-003',
            //         'model' => 'gpt-3.5-turbo',
            //         'prompt' => $formatQuestion,     // 设置问题
            //         'max_tokens' => 3000,   // 设置答案长度 不设置只显示部分字符
            //     ])
            // )->toModel();
            
            
            
        $client = new Client();
        $token = config('api.open_api_key');
        $url = "https://api.openai.com/v1/chat/completions";
        
        $formatQuestion = $this -> aiFormat($param['title'], $param['description'], $param['code']);
        // 获取接口数据
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36",
            ],
            'timeout' => 10,
            'verify' => false,
            'http_errors' => false,
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $formatQuestion
                    ]
                ]
            ]
        ];

        try {
            $res = $client->requestAsync('POST', $url, $options)->wait();
            $data = json_decode($res->getBody()->getContents(), true);
            dump($data);die;
            if (!isset($data['choices'][0]['message']['content'])) {
                Log::error('get chatgpt translate content fail no data: ' . json_encode($data));
            } else {
                Log::info('get chatgpt translate content usage: ' . json_encode($data['usage']));
            }
        } catch (\Throwable $th) {
            Log::debug(sprintf('ChatgptTranslate error: %s', $th->getMessage()));
        }
        return $data['choices'][0]['message']['content'] ?? null;
            
            
            
            
            
            dump($response);die;
            if ($response->choices[0]->text) {
                $this->updateKb($param['kb_id']);
                $ai = $this->saveAiAnswer($param['kb_id'],$response->choices[0]->text);
                return $ai->content;
            } else {
                $this->updateKb($param['kb_id']);
                TApiException('暂无数据', 40000);
            }
        }
    }

    public function upErrorAi() {
        $param = request()->param();
        $this->updateKb($param['kb_id']);
        return true;
    }

    public function answerList() {
        $param = request()->param();
        $list = $this->where('kb_id', $param['kb_id'])->where('is_delete', 1)->field('id, content, create_time')->find();
        if ($list) {
            $list['username'] = 'EdenAI';
            $list['userpic'] = 'https://zansen.s3.ap-east-1.amazonaws.com/zansen/8f535e482023111516104219163.png';
        }
        return $list;

    }

    public function verify($kb_id) {
        $answerVerify = (new KnowledgeBase())->where('id', $kb_id)->where('ai_status', 1)->find();
        return $answerVerify ? TApiException('Do not try again', 3900) : true;
    }

    public function aiFormat($title, $description, $code) {
        $question = 'title:' . $title . PHP_EOL . 'description:' . $description . PHP_EOL . 'code:' . $code;
        return $question;
    }

    public function updateKb($kb_id){
        (new KnowledgeBase())->where('id', $kb_id)->update(['ai_status'=>1]);
    }

    public function saveAiAnswer($kb_id, $content) {
        return $this->create([
            'kb_id' => $kb_id,
            'content' => trim($content),
        ]);
    }
}