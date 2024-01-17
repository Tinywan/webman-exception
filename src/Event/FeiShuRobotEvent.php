<?php
/**
 * @desc 飞书机器人
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/3/21 13:55
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler\Event;

class FeiShuRobotEvent
{
    /**
     * 发送机器人
     * @param array $args
     * @param array $config
     * @param string $name
     * @param string $text
     * @return bool|string
     */
    public static function send()
    {
        $accessToken = '70c44466-6fbc-4558-b454-e13c685dd18c';
        $url = 'https://open.feishu.cn/open-apis/bot/v2/hook/'.$accessToken;
        $timestamp = time();
        $secret = '7mZrqo3Fsy5iwxKY6FMxve';//秘钥
        $sign = $timestamp . "\n" . $secret;
        $sign = base64_encode(hash_hmac('sha256', '', $sign, true));
//        $body = [
//            'timestamp' => $timestamp,
//            'sign' => $sign,
//            'msg_type' => 'text',
//            'content' => ['text' => '这是一条测试数据这是一条测试数据这是一条测试数据']
//        ];
//        $postData = json_encode($data,JSON_UNESCAPED_UNICODE);
//        $options = [
//            'http' => [
//                'method' => 'POST',
//                'header' => 'Content-type:application/json;charset=UTF-8',
//                'content' => $postData,
//                'timeout' => 60
//            ]
//        ];
//        $context = stream_context_create($options);
//        $result = file_get_contents($url, false, $context);

        $message = '# 标题\n> 这是一个引用\n> 这是第二行';
        $data = [
            'timestamp' => $timestamp,
            'sign' => $sign,
            'msg_type' => 'post',
            'content' => [
                'zh_cn' => [
                    'title' => '测试消息markdownmarkdownmarkdownmarkdown',
                    'content' => [
                        [
                            [
                                'tag' => 'text',
                                'text' => '项目有更新项目有更新项目有更新项目有更新',
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $jsonData = json_encode($data);

        // 设置cURL选项
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));
        // 发送请求并获取响应
        $response = curl_exec($ch);
        var_dump($response);
        if ($response === false) {
            echo '请求失败: ' . curl_error($ch);
        } else {
            echo '请求成功';
        }
        curl_close($ch);
    }
}
