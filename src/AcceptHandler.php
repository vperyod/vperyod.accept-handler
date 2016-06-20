<?php
/**
 * Vperyod\AcceptHandler
 *
 * PHP version 5
 *
 * Copyright (C) 2016 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  Middleware
 * @package   Vperyod\AcceptHandler
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://jnj.mit-license.org/2016 MIT License
 * @link      https://github.com/vperyod/vperyod.accept-handler
 */

namespace Vperyod\AcceptHandler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Aura\Accept\AcceptFactory;

/**
 * AcceptHandler
 *
 * @category Middleware
 * @package  Vperyod\AcceptHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/2016 MIT License
 * @link     https://github.com/vperyod/vperyod.accept-handler
 */
class AcceptHandler
{
    use AcceptRequestAwareTrait;

    /**
     * Accept Factory Factory
     *
     * @var callable
     *
     * @access protected
     */
    protected $acceptFactoryFactory;

    /**
     * Media types
     * A map of file .extensions to media types
     *
     * @var array
     *
     * @access protected
     */
    protected $mediaTypes = [];

    /**
     * Create an AcceptHandler
     *
     * @param callable $acceptFactoryFactory factory to create an AcceptFactory
     *
     * @access public
     */
    public function __construct(callable $acceptFactoryFactory = null)
    {
        $this->acceptFactoryFactory = $acceptFactoryFactory
            ?: [$this, 'newAcceptFactory'];
    }

    /**
     * Adds Accept object to request
     *
     * @param Request  $request  PSR7 HTTP Request
     * @param Response $response PSR7 HTTP Response
     * @param callable $next     Next callable middleware
     *
     * @return Response
     *
     * @access public
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $request = $request->withAttribute(
            $this->acceptAttribute,
            $this->newAccept($request)
        );
        return $next($request, $response);
    }

    /**
     * Set media types
     *
     * @param array $types A map of file .extensions to media types
     *
     * @return $this
     *
     * @access public
     */
    public function setMediaTypes(array $types)
    {
        $this->mediaTypes = $types;
        return $this;
    }

    /**
     * Create a new AcceptFactory
     *
     * @param array $server representing $_SERVER
     * @param array $types  Media Types array
     *
     * @return AcceptFactory
     *
     * @access protected
     */
    protected function newAcceptFactory(array $server, array $types)
    {
        return new AcceptFactory($server, $types);
    }

    /**
     * Create a new Accept
     *
     * @param Request $request PSR7 Request
     *
     * @return Accept
     *
     * @access protected
     */
    protected function newAccept(Request $request)
    {
        $factoryFactory = $this->acceptFactoryFactory;
        return $factoryFactory($request->getServerParams(), $this->mediaTypes)
            ->newInstance();
    }
}
