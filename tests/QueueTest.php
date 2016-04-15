<?php

namespace Lucid\Infuse\Tests;

use Lucid\Infuse\Queue;
use Lucid\Signal\EventDispatcher;

class QueueTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Infuse\QueueInterface', new Queue($this->mockEvents()));
        $this->assertInstanceOf('Lucid\Infuse\MiddlewareInterface', new Queue($this->mockEvents()));
    }

    /** @test */
    public function itShouldPassResultResponses()
    {
        $q = new Queue($events = new EventDispatcher);

        $req = $this->mockRequest();
        $res = $this->mockResponse();

        $q->add($a = $this->mockMiddleware());
        $q->add($b = $this->mockMiddleware());
        $q->add($c = $this->mockMiddleware());


        $a->expects($this->once())->method('handle')->willReturnCallback(function (...$args) {
            return $args;
        });

        $c->expects($this->once())->method('handle')->willReturnCallback(function (...$args) {
            return $args;
        });

        $b->expects($this->once())->method('handle')->with($req)->willReturn([$req, $res]);

        list ($aq, $bq) = $q->handle($req);

        $this->assertSame($bq, $res);
    }

    /** @test */
    public function stoppedEventsMustBreakExecution()
    {
        $q = new Queue($events = new EventDispatcher);

        $events->addHandler('infuse.middleware', function ($event) {
            $event->stop();
        });

        $req = $this->mockRequest();
        $res = $this->mockResponse();

        $q->add($a = $this->mockMiddleware());
        $q->add($b = $this->mockMiddleware());
        $q->add($c = $this->mockMiddleware());

        $c->expects($this->once())->method('handle')->willReturnCallback(function (...$args) {
            return $args;
        });

        $a->expects($this->exactly(0))->method('handle');
        $b->expects($this->exactly(0))->method('handle');

        $q->handle($req);
    }

    /** @test */
    public function itEventShouldPassResponseAndRequest()
    {
        $q = new Queue($events = new EventDispatcher);

        $req = $this->mockRequest();
        $res = $this->mockResponse();

        $events->addHandler('infuse.middleware', function ($event) {
            $event->setRequest($this->mockRequest());
            $event->setResponse($this->mockResponse());
        });

        $q->add($a = $this->mockMiddleware());

        $a->expects($this->once())->method('handle')->willReturnCallback(function (...$args) {
            return $args;
        });

        list ($aq, $bq) = $q->handle($req);
        $this->assertFalse($aq === $req);
        $this->assertFalse($bq === $res);
    }

    private function mockEvents()
    {
        return $this->getMockbuilder('Lucid\Signal\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockMiddleware()
    {
        return $this->getMockbuilder('Lucid\Infuse\MiddlewareInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockRequest()
    {
        return $this->getMockbuilder('Psr\Http\Message\ServerRequestInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockResponse()
    {
        return $this->getMockbuilder('Psr\Http\Message\ResponseInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
