<?php
/**
 * @desc 团队隔离
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/6 14:14
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler\Exception;

class NotTeamMemberException extends BaseException
{
    /**
     * HTTP 状态码
     */
    public $statusCode = 403;

    /**
     * 错误消息.
     */
    public $errorMessage = '您无权访问该资源';

    /**
     * @var array|string[]
     */
    public $data = [
        'id' => 'tinywan2022',
        'name' => '团队名称：开源技术小栈'
    ];
}
