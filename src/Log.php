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
    public static function __callStatic($name, $arguments)
    {
        if ($name === 'error') {
            // todo 触发钉钉机器人报警
        }
        return parent::__callStatic($name, $arguments);
    }
}