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
    public int $statusCode = 403;

    /**
     * 错误消息.
     */
    public string $errorMessage = '您无权访问该资源';

    /**
     * @var array|string[]
     */
    public array $data = [
        'id' => 'tinywan2028',
        'name' => '开源技术小栈'
    ];
}
