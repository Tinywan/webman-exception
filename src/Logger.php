<?php
/**
 * @desc Log
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
                'domain' => $original['domain'] ?? request()->host(),
                'request_url' => $original['request_url'] ?? request()->uri(),
                'client_ip' => $original['client_ip'] ?? request()->getRealIp(),
                'timestamp' => $original['timestamp'] ?? date('Y-m-d H:i:s'),
                'request_param' => $original['request_param'] ?? request()->all(),
                'file' => $original['file'] ?? '--',
                'line' => $original['line'] ?? '--',
            ];
            $title = '开发环境';
            if (isset($args['domain'])) {
                if (strstr($args['domain'], $config['domain']['test'] ?? '')) {
                    $title = '测试环境';
                } elseif (strstr($args['domain'], $config['domain']['pre'] ?? '')) {
                    $title = '预发环境';
                } elseif (strstr($args['domain'], $config['domain']['prod'] ?? '')) {
                    $title = '正式环境';
                }
            }
            DingTalkRobotEvent::dingTalkRobot($args, $config, $title);
        }
        return parent::__callStatic($name, $arguments);
    }
}