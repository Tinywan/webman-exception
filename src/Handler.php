<?php
/**
 * @desc ExceptionHandler
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/6 14:08
 */

declare(strict_types=1);

namespace Tinywan\ExceptionHandler;

use Throwable;
use Tinywan\ExceptionHandler\Event\DingTalkRobotEvent;
use Tinywan\ExceptionHandler\Exception\BaseException;
use Tinywan\Jwt\Exception\JwtTokenException;
use Tinywan\Validate\Exception\ValidateException;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * 不需要记录错误日志.
     *
     * @var string[]
     */
    public $dontReport = [];

    /**
     * @param Throwable $exception
     */
    public function report(Throwable $exception)
    {
        $this->dontReport = config('plugin.tinywan.exception-handler.app.exception_handler.dont_report', []);
        parent::report($exception);
    }

    /**
     * @desc: 方法描述
     * @param Request $request
     * @param Throwable $e
     * @return Response
     * @author Tinywan(ShaoBo Wan)
     */
    public function render(Request $request, Throwable $e): Response
    {
        $responseData = [
            'request_url' => $request->method() . ' ' . $request->fullUrl(),
            'timestamp' => date('Y-m-d H:i:s'),
            'client_ip' => $request->getRealIp(),
            'request_param' => $request->all()
        ];
        $errorCode = 0;
        $header = [];
        if ($e instanceof BaseException) {
            $statusCode = $e->statusCode;
            $header = $e->header;
            $errorMessage = $e->errorMessage;
            $errorCode = $e->errorCode;
            if ($e->data) {
                $responseData = array_merge($responseData, $e->data);
            }
        } else {
            $errorMessage = $e->getMessage();
            if ($e instanceof ValidateException) {
                $statusCode = 400;
            } elseif ($e instanceof JwtTokenException) {
                $statusCode = 401;
                $errorMessage = $e->getMessage();
            } elseif ($e instanceof \InvalidArgumentException) {
                $statusCode = 415;
                $errorMessage = '预期参数配置异常：' . $e->getMessage();
            } else {
                $statusCode = 500;
                $errorMessage = $e->getMessage();
                $errorCode = 50000;
            }
        }
        if (config('app.debug')) {
            $responseData['error_message'] = $errorMessage;
            $responseData['error_trace'] = $e->getTraceAsString();
        }

        $config = config('plugin.tinywan.exception-handler.app.event');
        if ($config['enable']) {
            $responseData['message'] = $errorMessage;
            $responseData['file'] = $e->getFile();
            $responseData['line'] = $e->getLine();
            DingTalkRobotEvent::dingTalkRobot($responseData);
        }
        $header = array_merge(['Content-Type' => 'application/json;charset=utf-8'], $header);
        return new Response($statusCode, $header, json_encode(['code' => $errorCode, 'msg' => $errorMessage,'data' => $responseData]));
    }
}
