<?php

return [
    'enable' => true,
    // 错误异常配置
    'exception_handler' => [
        // 不需要记录错误日志
        'dont_report' => [
            Tinywan\ExceptionHandler\Exception\BadRequestHttpException::class,
            Tinywan\ExceptionHandler\Exception\UnauthorizedHttpException::class,
            Tinywan\ExceptionHandler\Exception\ForbiddenHttpException::class,
            Tinywan\ExceptionHandler\Exception\NotFoundHttpException::class,
            Tinywan\ExceptionHandler\Exception\RouteNotFoundException::class,
            Tinywan\ExceptionHandler\Exception\TooManyRequestsHttpException::class,
            Tinywan\ExceptionHandler\Exception\ServerErrorHttpException::class,
            Tinywan\Validate\Exception\ValidateException::class,
            Tinywan\Jwt\Exception\JwtTokenException::class
        ],
        // 自定义HTTP状态码
        'status' => [
            'validate' => 400, // 验证器异常
            'jwt_token' => 401, // 认证失败
            'jwt_token_expired' => 401, // 访问令牌过期
            'jwt_refresh_token_expired' => 402, // 刷新令牌过期
            'server_error' => 500, // 服务器内部错误
            'server_error_is_response' => false, // 是否响应服务器内部错误
            'type_error' => 400, // 参数类型错误码
            'type_error_is_response' => false, // 参数类型与预期声明的参数类型不匹配
        ],
        // 自定义响应消息
        'body' => [
            'code' => 0,
            'msg' => '服务器内部异常',
            'data' => null
        ],
        // 事件，event 与 webman/event 存在冲突，event 重命名为 event_trigger
        'event_trigger' => [
            'enable' => false,
            // 钉钉机器人
            'dingtalk' => [
                'accessToken' => 'xxxxxxxxxxxxxxxx',
                'secret' => 'xxxxxxxxxxxxxxxx',
                'title' => '钉钉机器人异常通知',
            ]
        ],
        /** 异常报警域名标题 */
        'domain' => [
            'dev' => 'dev-api.tinywan.com', // 开发环境
            'test' => 'test-api.tinywan.com', // 测试环境
            'pre' => 'pre-api.tinywan.com', // 预发环境
            'prod' => 'api.tinywan.com',  // 生产环境
        ],
        /** 是否生产环境 。可以通过配置文件或者数据库读取返回 eg：return config('app.env') === 'prod';*/
        'is_prod_env' => function () {
            return false;
        },
    ],

];