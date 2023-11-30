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

class Log extends \support\Log
{
    /**
     * @desc __callStatic
     * @param $name
     * @param $arguments
     * @return mixed
     * @author Tinywan(ShaoBo Wan)
     */
    public static function __callStatic($name, $arguments)
    {
        // todo 触发钉钉机器人报警
        if ($name === 'error') {
            $message = $arguments[0] ?? '未知错误';
            $data = $arguments[1] ?? [];
            $config = config('plugin.tinywan.exception-handler.app.exception_handler', []);
            DingTalkRobotEvent::dingTalkRobot($arguments, $config);
        }
        return parent::__callStatic($name, $arguments);
    }
}