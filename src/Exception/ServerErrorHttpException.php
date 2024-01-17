<?php
/**
 * @desc 服务器内部异常
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.5.3
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/3/6 14:14
 * @since 1.0
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler\Exception;

/**
 * ServerErrorHttpException represents an "Internal Server Error" HTTP exception with status code 500.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.6.1
 * @since 2.0
 */
class ServerErrorHttpException extends BaseException
{
    /**
     * @var int
     */
    public int $statusCode = 500;

    /**
     * @var string
     */
    public string $errorMessage = 'Internal Server Error';
}
