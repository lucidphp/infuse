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

/**
 * @interface StackInterface
 *
 * @package Lucid\Infuse
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface QueueInterface extends MiddlewareInterface
{
    /**
     * Adds a middleware to the queue.
     *
     * @param MiddlewareInterface $middleware
     * @param int $priority
     *
     * @return self
     */
    public function add(MiddlewareInterface $middleware, $priority = null);
}
