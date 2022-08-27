<?php
/**
 * @desc DingTalkRobotEvent.php 钉钉机器人
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/3/21 13:55
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler\Event;

class DingTalkRobotEvent
{
    /**
     * 发送钉钉机器人
     * @param array $args
     * @return bool|string
     */
    public static function dingTalkRobot(array $args)
    {
        $config =  config('plugin.tinywan.exception-handler.app.exception_handler.event_trigger.ding_talk');
        $accessToken = $config['accessToken'];
        $secret = $config['secret'];
        $title = $config['title'];
        $message = ' --- ' . " \n";
        $message .= ' - 请求时间：' . $args['timestamp'] . " \n";
        $message .= ' - 请求路由：' . $args['request_url'] . " \n";
        $message .= " - 请求IP：" . $args['client_ip'] . " \n";
        $message .= ' - 请求参数：' . json_encode($args['request_param']) . " \n";
        $message .= ' - 异常消息：' . $args['message'] . " \n";
        $message .= ' - 异常文件：' . $args['file'] . " \n";
        $message .= ' - 异常文件行数：' . $args['line'] . " \n";
        $data = [
            'msgtype' => 'markdown',
            'markdown' => [
                'title' => $title,
                'text' => $message
            ],
            'at' => [
                'isAtAll' => true
            ]
        ];
        $orderPayUrl = 'https://oapi.dingtalk.com/robot/send?access_token=' . $accessToken;
        return  self::request_by_curl(self::_sign($orderPayUrl, $secret), json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @desc: 请求签名
     * @param string $url
     * @param string $secret
     * @return string
     * @author Tinywan(ShaoBo Wan)
     */
    private static function _sign(string $url, string $secret): string
    {
        [$s1, $s2] = explode(' ', microtime());
        $timestamp = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
        $data = $timestamp . "\n" . $secret;
        $signStr = base64_encode(hash_hmac('sha256', $data, $secret, true));
        $signStr = utf8_encode(urlencode($signStr));
        return $url . "&timestamp=$timestamp&sign=$signStr";
    }

    /**
     * @desc: 自定义请求类
     * @param string $remote_server
     * @param string $postString
     * @return bool|string
     * @author Tinywan(ShaoBo Wan)
     */
    private static function request_by_curl(string $remote_server, string $postString)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
