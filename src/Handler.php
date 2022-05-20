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
     * 异常信息数据.
     *
     * @var array
     */
    protected $exceptionInfo = [
        'statusCode' => 0,
        'responseHeader' => [],
        'errorCode' => 0,
        'errorMsg' => '',
    ];

    /**
     * 响应结果数据.
     *
     * @var array
     */
    protected $responseData = [];

    /**
     * config下的配置.
     *
     * @var array
     */
    protected $config = [];

    /**
     * @param Throwable $exception
     */
    public function report(Throwable $exception)
    {
        $this->dontReport = config('plugin.tinywan.exception-handler.app.exception_handler.dont_report', []);
        parent::report($exception);
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception): Response
    {
        $this->config = array_merge($this->config, config('plugin.tinywan.exception-handler.app.exception_handler', []));

        $this->addRequestInfoToResponse($request);
        $this->solveAllException($exception);
        $this->addDebugInfoToResponse($exception);
        $this->triggerNotifyEvent($exception);
        $this->triggerTraceEvent($exception);

        return $this->buildResponse();
    }

    /**
     * 请求的相关信息.
     *
     * @param Request $request
     * @return void
     */
    protected function addRequestInfoToResponse(Request $request): void
    {
        $this->responseData = array_merge($this->responseData, [
            'request_url' => $request->method() . ' ' . $request->fullUrl(),
            'timestamp' => date('Y-m-d H:i:s'),
            'client_ip' => $request->getRealIp(),
            'request_param' => $request->all(),
        ]);
    }

    /**
     * 处理异常数据.
     *
     * @param Throwable $e
     */
    protected function solveAllException(Throwable $e)
    {
        // 处理常用的 http 异常
        if ($e instanceof BaseException) {
            $this->exceptionInfo['statusCode'] = $e->statusCode;
            $this->exceptionInfo['responseHeader'] = $e->header;
            $this->exceptionInfo['errorMsg'] = $e->errorMessage;
            $this->exceptionInfo['errorCode'] = $e->errorCode;
            if ($e->data) {
                $this->responseData = array_merge($this->responseData, $e->data);
            }
            return;
        }
        // 处理扩展的其他异常
        $this->solveExtraException($e);
    }

    /**
     * 处理扩展的异常.
     *
     * @param Throwable $e
     */
    protected function solveExtraException(Throwable $e): void
    {
        $status = $this->config['status'];

        $this->exceptionInfo['errorMsg'] = $e->getMessage();
        if ($e instanceof \Tinywan\Validate\Exception\ValidateException) {
            $this->exceptionInfo['statusCode'] = $status['validate'];
        } elseif ($e instanceof \Tinywan\Jwt\Exception\JwtTokenException) {
            $this->exceptionInfo['statusCode'] = $status['jwt_token'];
        } elseif ($e instanceof \Tinywan\Jwt\Exception\JwtTokenExpiredException) {
            $this->exceptionInfo['statusCode'] = $status['jwt_token_expired'];
        } elseif ($e instanceof \InvalidArgumentException) {
            $this->exceptionInfo['statusCode'] = $status['invalid_argument'] ?? 415;
            $this->exceptionInfo['errorMsg'] = '预期参数配置异常：' . $e->getMessage();
        } else {
            $this->exceptionInfo['statusCode'] = $status['server_error'];
            $this->exceptionInfo['errorCode'] = 50000;
        }
    }

    /**
     * 添加 debug 信息到 response.
     *
     * @param Throwable $e
     * @return void
     */
    protected function addDebugInfoToResponse(Throwable $e): void
    {
        if (config('app.debug', false)) {
            $this->responseData['error_message'] = $this->exceptionInfo['errorMsg'];
            $this->responseData['error_trace'] = explode("\n", $e->getTraceAsString());
        }
    }

    /**
     * 触发通知事件.
     *
     * @param Throwable $e
     * @return void
     */
    protected function triggerNotifyEvent(Throwable $e): void
    {
        if ($this->config['event']['enable'] ?? false) {
            $responseData['message'] = $this->exceptionInfo['errorMsg'];
            $responseData['file'] = $e->getFile();
            $responseData['line'] = $e->getLine();
            DingTalkRobotEvent::dingTalkRobot($responseData);
        }
    }

    /**
     * 触发 trace 事件.
     *
     * @param Throwable $e
     * @return void
     */
    protected function triggerTraceEvent(Throwable $e): void
    {
        if (isset(request()->tracer) && isset(request()->rootSpan)) {
            $samplingFlags = request()->rootSpan->getContext();
            $this->exceptionInfo['header']['Trace-Id'] = $samplingFlags->getTraceId();
            $exceptionSpan = request()->tracer->newChild($samplingFlags);
            $exceptionSpan->setName('exception');
            $exceptionSpan->start();
            $exceptionSpan->tag('error.code', (string)$this->exceptionInfo['errorCode']);
            $value = [
                'event' => 'error',
                'message' => $this->exceptionInfo['errorMsg'],
                'stack' => 'Exception:' . $e->getFile() . '|' . $e->getLine(),
            ];
            $exceptionSpan->annotate(json_encode($value));
            $exceptionSpan->finish();
        }
    }

    /**
     * 构造 Response.
     *
     * @return Response
     */
    protected function buildResponse(): Response
    {
        $bodyKey = array_keys($this->config['body']);
        $responseBody = [
            $bodyKey[0] ?? 'code' => $this->exceptionInfo['errorCode'],
            $bodyKey[1] ?? 'msg' => $this->exceptionInfo['errorMsg'],
            $bodyKey[2] ?? 'data' => $this->responseData,
        ];

        $header = array_merge(['Content-Type' => 'application/json;charset=utf-8'], $this->exceptionInfo['responseHeader']);
        return new Response($this->exceptionInfo['statusCode'], $header, json_encode($responseBody));
    }
}
