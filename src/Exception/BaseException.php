<?php
/**
 * @desc BaseException
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/6 14:14
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler\Exception;

class BaseException extends \Exception
{
    /**
     * HTTP Response Status Code.
     */
    public $statusCode = 400;

    /**
     * HTTP Response Header.
     */
    public $header = [];

    /**
     * Business Error code.
     *
     * @var int|mixed
     */
    public $errorCode = 0;

    /**
     * Business Error message.
     * @var string
     */
    public $errorMessage = 'The requested resource is not available or not exists';

    /**
     * Business data.
     * @var array|mixed
     */
    public $data = [];

    /**
     * BaseException constructor.
     * @param string $errorMessage
     * @param array $params
     */
    public function __construct(string $errorMessage = '', array $params = [])
    {
        parent::__construct();
        if (!empty($errorMessage)) {
            $this->errorMessage = $errorMessage;
        }
        if (!empty($params)) {
            if (array_key_exists('statusCode', $params)) {
                $this->statusCode = $params['statusCode'];
            }
            if (array_key_exists('header', $params)) {
                $this->header = $params['header'];
            }
            if (array_key_exists('errorCode', $params)) {
                $this->errorCode = $params['errorCode'];
            }
            if (array_key_exists('data', $params)) {
                $this->data = $params['data'];
            }
        }
    }
}
