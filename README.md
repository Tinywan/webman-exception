# webman exception handler 异常插件

[![Latest Stable Version](http://poser.pugx.org/tinywan/exception-handler/v)](https://packagist.org/packages/tinywan/exception-handler)
[![Total Downloads](http://poser.pugx.org/tinywan/exception-handler/downloads)](https://packagist.org/packages/tinywan/exception-handler)
[![License](http://poser.pugx.org/tinywan/exception-handler/license)](https://packagist.org/packages/tinywan/exception-handler)
[![PHP Version Require](http://poser.pugx.org/tinywan/exception-handler/require/php)](https://packagist.org/packages/tinywan/exception-handler)
[![exception-handler](https://img.shields.io/github/last-commit/tinywan/exception-handler/main)]()
[![exception-handler](https://img.shields.io/github/v/tag/tinywan/exception-handler?color=ff69b4)]()

## 安装

```php
composer require tinywan/exception-handler
```

## 配置

`config/exception.php`

```php
return [
    // 这里配置异常处理类
    '' => \Tinywan\ExceptionHandler\Handler::class,
];
```
> 多应用模式时，你可以为每个应用单独配置异常处理类，参见[多应用](https://www.workerman.net/doc/webman/multiapp.html)

## 基本用法

请求参数错误
```php
use support\Request;
use support\Response;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class Token{
    public function issueToken(Request $request): Response
    {
        $params = $request->post();
        if (empty($params)) {
            throw new BadRequestHttpException('账号或密码不能为空');
        }
    }
}
```
以上异常抛出错误信息，如下格式：
```json
HTTP/1.1 400 Bad Request
Content-Type: application/json;charset=utf-8

{
    "code": 0,
    "msg": "账号或密码不能为空",
    "data": {},
}
```
> 所有返回的异常信息将以json格式返回，以上为`返回简略的异常信息`

所有的异常错误处理器根据配置文件 `config/app.php`中`debug`的值来调整错误显示， 当`debug`值为`true` (表示在调试模式)， 错误处理器会显示异常以及详细的函数调用栈和源代码行数来帮助调试，将返回详细的异常信息。 当`debug`值为`false`，只有错误信息会被显示以防止应用的敏感信息泄漏，将返回简略的异常信息。

返回详细的异常信息
```json
HTTP/1.1 400 Bad Request
Content-Type: application/json;charset=utf-8
        
{
    "code": 0,
    "msg": "password不允许为空",
    "data": {
        "request_url": "POST //127.0.0.1:8888/oauth/issue-token",
        "timestamp": "2022-03-06 15:19:12",
        "client_ip": "172.18.0.1",
        "request_param": {
            "username": "webman"
        },
        "error_message": "password不允许为空",
        "error_trace": "#0 /var/www/webman-admin/app/functions.php(68): Tinywan\\Validate\\Validate->check(Array)\n#1 /var/www/webman-admin/app/controller/Authentication.php(25): validate(Array, 'app\\\\common\\\\vali...')\n#2 /var/www/webman-admin/vendor/workerman/webman-framework/src/App.php(234): app\\controller\\Authentication->issueToken(Object(support\\Request))\n#3 /var/www/webman-admin/app/middleware/AccessControlMiddleware.php(26): Webman\\App::Webman\\{closure}(Object(support\\Request))\n#4 /var/www/webman-admin/vendor/workerman/webman-framework/src/App.php(228): app\\middleware\\AccessControlMiddleware->process(Object(support\\Request), Object(Closure))\n#5 /var/www/webman-admin/vendor/workerman/webman-framework/src/App.php(137): Webman\\App::Webman\\{closure}(Object(support\\Request))\n#6 /var/www/webman-admin/vendor/workerman/workerman/Connection/TcpConnection.php(638): Webman\\App->onMessage(Object(Workerman\\Connection\\TcpConnection), Object(support\\Request))\n#7 /var/www/webman-admin/vendor/workerman/workerman/Events/Select.php(295): Workerman\\Connection\\TcpConnection->baseRead(Resource id #254)\n#8 /var/www/webman-admin/vendor/workerman/workerman/Worker.php(2417): Workerman\\Events\\Select->loop()\n#9 /var/www/webman-admin/vendor/workerman/workerman/Worker.php(1541): Workerman\\Worker->run()\n#10 /var/www/webman-admin/vendor/workerman/workerman/Worker.php(1383): Workerman\\Worker::forkOneWorkerForLinux(Object(Workerman\\Worker))\n#11 /var/www/webman-admin/vendor/workerman/workerman/Worker.php(1357): Workerman\\Worker::forkWorkersForLinux()\n#12 /var/www/webman-admin/vendor/workerman/workerman/Worker.php(549): Workerman\\Worker::forkWorkers()\n#13 /var/www/webman-admin/start.php(87): Workerman\\Worker::runAll()\n#14 {main}"
    }
}
```

## 如何自定义一个自己的异常类

### 编写异常类

假设自定义一个：`405 Method Not Allowed`（表示：请求行中指定的请求方法不能被用于请求相应的资源）

自定义异常类只需要继承`Tinywan\ExceptionHandler\Exception\BaseException`类即可

```php
<?php
declare(strict_types=1);

namespace support\exception;

use Tinywan\ExceptionHandler\Exception\BaseException;

class MethodNotAllowedException extends BaseException
{
    /**
     * @var int
     */
    public int $statusCode = 405;

    /**
     * @var string
     */
    public string $errorMessage = '请求行中指定的请求方法不能被用于请求相应的资源';
}
```

### 使用异常类

```php
use support\Request;
use support\Response;
use support\exception\MethodNotAllowedException;

class Token{
    public function issueToken(Request $request): Response
    {
        $params = $request->post();
        if (empty($params)) {
            throw new MethodNotAllowedException();
        }
    }
}
```

使用postman请求截图

![self-exception.png](self-exception.png)

## 已支持插件异常类

- [Validate 验证器插件](https://www.workerman.net/plugin/7) 异常类`JwtTokenException`
- [JWT 权限认证插件](https://www.workerman.net/plugin/10) 异常类`ValidateException`

## 内置异常类

- 客户端异常类（HTTP Status 400）：BadRequestHttpException
- 身份认证异常类（HTTP Status 401）：UnauthorizedHttpException
- 资源授权异常类（HTTP Status 403）：ForbiddenHttpException
- 资源不存在异常类（HTTP Status 404）：NotFoundHttpException
- 路由地址不存在异常类（HTTP Status 404）：RouteNotFoundException
- 请求限流在异常类（HTTP Status 429）：TooManyRequestsHttpException
- 服务器内部错误异常类（HTTP Status 500）：ServerErrorHttpException

[更多：https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Status](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Status)

## 异常通知（钉钉机器人）

![dingtalk.png](dingtalk.png)

## Other

### phpstan

```phpregexp
vendor/bin/phpstan analyse src
```

### vendor/bin/php-cs-fixer fix src

```phpregexp
vendor/bin/php-cs-fixer fix src
```