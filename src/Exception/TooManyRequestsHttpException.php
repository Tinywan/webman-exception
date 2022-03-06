<?php
/**
 * @desc 为了保证服务可用性和安全性，防止服务器受到恶意攻击，服务端对调用频率做了限制
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/3/6 14:14
 * @since 1.0
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler\Exception;

class TooManyRequestsHttpException extends BaseException
{
    /**
     * @var int
     */
    public int $statusCode = 429;

    /**
     * @var array
     */
    public array $header = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Headers' => 'Authorization,Content-Type,If-Match,If-Modified-Since,If-None-Match,If-Unmodified-Since,X-Requested-With,Origin',
        'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE,OPTIONS',
        'X-Rate-Limit-Limit' => 0, //  同一个时间段所允许的请求的最大数目;
        'X-Rate-Limit-Remaining' => 0, // 在当前时间段内剩余的请求的数量;
        'X-Rate-Limit-Reset' => 0 // 为了得到最大请求数所等待的秒数。
    ];

    /**
     * @var int
     */
    public int $errorCode = 0;

    /**
     * @var string
     */
    public string $errorMessage = "Too Many Requests, Please try again later";
}
