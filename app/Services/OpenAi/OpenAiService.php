<?php
namespace App\Services\OpenAi;
use Orhanerday\OpenAi\OpenAi;

// Dummy Response For Chat API
$j = '
    {
       "id":"chatcmpl-*****",
       "object":"chat.completion",
       "created":1679748856,
       "model":"gpt-3.5-turbo-0301",
       "usage":{
          "prompt_tokens":9,
          "completion_tokens":10,
          "total_tokens":19
       },
       "choices":[
          {
             "message":{
                "role":"assistant",
                "content":"This is a test of the AI language model."
             },
             "finish_reason":"length",
             "index":0
          }
       ]
    }
    ';
class OpenAiService{
    private $openAi;
    private $functionHandler;
    public function __construct(OpenAi $openAi, FunctionHandler $functionHandler)
    {
        $this->openAi = $openAi;
        $this->functionHandler = $functionHandler;
    }

    public function chat($messages, array $toolNames = [], $temperature = 1.0, $maxTokens = 4000, $frequencyPenalty = 0, $presencePenalty = 0)
    {
        $tools = FunctionRegistry::getTools($toolNames); // 필요한 함수만 가져오기

        $request = [
            'model' => 'gpt-4o',
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'frequency_penalty' => $frequencyPenalty,
            'presence_penalty' => $presencePenalty
        ];

        if (!empty($tools)) {
            $request['tools'] = $tools;
        }

        $chat = $this->openAi->chat($request);
        $d = json_decode($chat, true);

        try {
            if (isset($d['choices'][0]['message']['tool_calls'])) {
                return $this->handleFunctionCalls($messages, $d);
            }

            if(isset($d['error'])){
                throw new \Exception($d['error']['message']);
            }

            return $d['choices'][0]['message']['content'];
        } catch (\Exception $e){
            throw $e;
        }
    }


    private function handleFunctionCalls($messages, $d)
    {
        foreach ($d['choices'][0]['message']['tool_calls'] as $toolCall) {
            $functionName = $toolCall['function']['name'];
            $params = json_decode($toolCall['function']['arguments'], true);

            try {
                $result = $this->functionHandler->handle($functionName, $params);

                // AI에게 함수 실행 결과 전달
                $messages[] = [
                    'role' => 'function',
                    'name' => $functionName,
                    'content' => json_encode($result)
                ];

                return $this->chat($messages, [$functionName]); // 함수 실행 후 재귀 호출
            } catch (\Exception $e) {
                return "Error: " . $e->getMessage();
            }
        }
    }

}
