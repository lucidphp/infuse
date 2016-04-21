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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @interface MiddlewareInterface
 *
 * @package Lucid\Infuse
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MiddlewareInterface
{
    /**
     * handle
     *
     * @param Request $request
     * @param Response $response
     *
     * @return array [Request, Response]
     */
    public function handle(Request $request, Response $response);
}
