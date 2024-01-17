<?php
/**
 * 由于明显的客户端错误（例如，格式错误的请求语法，太大的大小，无效的请求消息或欺骗性路由请求），服务器不能或不会处理该请求。
 * BadRequestHttpException represents a "Bad Request" HTTP exception with status code 400.
 *
 * Use this exception to represent a generic client error. In many cases, there
 * may be an HTTP exception that more precisely describes the error. In that
 * case, consider using the more precise exception to provide the user with
 * additional information.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.5.1
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/6 14:14
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler\Exception;

class BadRequestHttpException extends BaseException
{
    /**
     * @var int
     */
    public int $statusCode = 400;

    /**
     * @var string
     */
    public string $errorMessage = '请求参数错误，服务器不能或不会处理该请求';
}
