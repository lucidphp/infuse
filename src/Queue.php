<?php

/*
 * This File is part of the Lucid\Infuse package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Infuse;

use SplPriorityQueue;
use Lucid\Infuse\Events\RequestEvent;
use Lucid\Signal\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @class Queue
 *
 * @package Lucid\Infuse
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Queue implements QueueInterface
{
    /** @var SplPriorityQueue */
    private $queue;

    /** @var EventDispatcherInterface */
    private $events;

    /** @var string */
    private $eventName;

    /**
     * Consructor.
     *
     * @param EventDispatcherInterface $events
     * @param string $eventName
     */
    public function __construct(EventDispatcherInterface $events, $eventName = 'infuse.middleware')
    {
        $this->events    = $events;
        $this->eventName = $eventName;
        $this->queue     = new SplPriorityQueue;
    }

    /**
     * {@inheritdoc}
     */
    public function add(MiddlewareInterface $middleware, $priority = null)
    {
        $this->queue->insert($middleware, $priority ?: $this->queue->count());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, Response $response)
    {
        $continue = true;

        while ($this->queue->valid() && $continue) {
            list ($request, $response, $continue) =
                call_user_func_array([$this, 'doHandle'], $this->queue->current()->handle($request, $response));
            $this->queue->next();
        }

        $this->queue->rewind();

        return [$request, $response];
    }

    /**
     * doHandle
     *
     * @param Request $request
     * @param Response $response
     *
     * @return array [Request, Response, bool]
     */
    private function doHandle(Request $request, Response $response)
    {
        $this->events->dispatch($this->eventName, $event = new RequestEvent($request, $response));

        return [$event->getRequest(), $event->getResponse(), !$event->isStopped()];
    }
}
