<?php
/**
 * @desc Logger
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2023/11/30 22:04
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler;


use Tinywan\ExceptionHandler\Event\DingTalkRobotEvent;

class Logger extends \support\Log
{
    /**
     * @desc __callStatic
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @author Tinywan(ShaoBo Wan)
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if ($name === 'error') {
            $config = config('plugin.tinywan.exception-handler.app.exception_handler', []);
            $original = $arguments[1] ?? [];
            $args = [
                'message' => current($arguments),
                'error' => $original['error'] ?? '--',
                'domain' => $original['domain'] ?? '--',
                'request_url' => $original['request_url'] ?? '--',
                'client_ip' => $original['client_ip'] ?? '127.0.0.1',
                'timestamp' => $original['timestamp'] ?? date('Y-m-d H:i:s'),
                'request_param' => $original['request_param'] ?? [],
                'file' => $original['file'] ?? '--',
                'line' => $original['line'] ?? '--',
            ];
            /**是否命令行模式*/
            if (!empty(request())) {
                $args['domain'] = request()->host();
                $args['request_url'] = request()->uri();
                $args['client_ip'] = request()->getRealIp();
                $args['request_param'] = request()->all();
            }
            $title = '开发环境';
            if (isset($args['domain']) && isset($config['domain'])) {
                if (strstr($args['domain'], $config['domain']['test'] ?? '')) {
                    $title = '测试环境';
                } elseif (strstr($args['domain'], $config['domain']['pre'] ?? '')) {
                    $title = '预发环境';
                } elseif (strstr($args['domain'], $config['domain']['prod'] ?? '')) {
                    $title = '正式环境';
                }
            }
            DingTalkRobotEvent::dingTalkRobot($args, $config, $title);
            return parent::__callStatic($name, $arguments);
        }
    }
}