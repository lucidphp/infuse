# Psr7 Middleware queue and interface

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/infuse-blue.svg?style=flat-square)](https://github.com/lucidphp/infuse/tree/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/infuse/blob/master/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/infuse/master.svg?style=flat-square)](https://travis-ci.org/lucidphp/infuse)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/infuse/master.svg?style=flat-square)](https://coveralls.io/r/lucidphp/infuse)
[![HHVM](https://img.shields.io/hhvm/lucid/infuse/dev-master.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/infuse)      

## Installation

```sh
> composer require lucid/infuse
```

## Requirements

- php >= 5.6
- psr/http-message
- lucid/signal

## Usage

### The middleware

Middlewares must implement `Lucid\Infuse\MiddlewareInterface`. They also must return
an array of `[Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $respoonse]`.

```php
<?php

namespace Acme\Middleware;

use Lucid\Infuse\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessoionHandler implements MiddlewareInterface
{
    public function handle(Request $request, Response $response)
    {
        // ...

        return [$request, $response];
    }
}
```

### The middleware queue

Use `QueueInterface::add()` to add middlewares to the execution queue.
Middlewares are executed first in last out.
The middleware queue itself implements `Lucid\Infuse\MiddlewareInterface` and
acts as an entrypoint.


```php
<?php

use Lucid\Infuse\Queue;
use Lucid\Signale\EventDispatcher;

$queue = new Queue(new EventDispatcher);

$queue->add($middlewareOmega); // will execute last
// …
$queue->add($middlewareAlpha); // fill execute first

// …
list ($request, $response) = $queue->handle($request, $response);

```

### Intercept execution

During middleware exection, the eventdispatcher will fire a request event.
Execution will stop if a middleware event is being stopped.

```php
<?php

use Lucid\Infuse\Queue;
use Lucid\Signale\EventDispatcher;
use Lucid\Infuse\Events\RequestEvent;

$queue = new Queue($events = new EventDispatcher, 'middleware');

$events->addHandler('middleware', function (RequestEvent $event) {
    $req = $event->getRequest();
    $res = $event->getResponse();

    if (…) {
        $event->setResponse(…);
        $event->stop();
    }
});

```
