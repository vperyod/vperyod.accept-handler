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
 * @category  Responder
 * @package   Vperyod\AcceptHandler
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://jnj.mit-license.org/2016 MIT License
 * @link      https://github.com/vperyod/vperyod.accept-handler
 */

namespace Vperyod\AcceptHandler\Responder;

use Vperyod\AcceptHandler\Exception;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ResponderLocator
 *
 * @category Responder
 * @package  Vperyod\AcceptHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/ MIT License
 * @link     https://github.com/vperyod/vperyod.accept-handler
 *
 * @abstract
 */
abstract class AbstractResponderLocator
{

    /**
     * Responder factories
     *
     * @var array
     *
     * @access protected
     */
    protected $factories = [];

    /**
     * Request
     *
     * @var Request
     *
     * @access protected
     */
    protected $request;

    /**
     * Response
     *
     * @var Response
     *
     * @access protected
     */
    protected $response;

    /**
     * Payload
     *
     * @var mixed
     *
     * @access protected
     */
    protected $payload;

    /**
     * Create a responder locator
     *
     * @param array $factories factories to create responders
     *
     * @access public
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * Negotiate and respond
     *
     * @param Request          $request  PSR7 Request
     * @param Response         $response PSR7 Response
     * @param PayloadInterface $payload  Domain Payload
     *
     * @return Response
     *
     * @access public
     */
    public function __invoke(
        Request $request,
        Response $response,
        $payload = null
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->payload = $payload;

        $type = $this->negotiate($request);

        if (! $type || ! $this->has($type)) {
            return $this->notAcceptable();
        }

        $responder = $this->get($type);

        return $responder($request, $response, $payload);
    }

    /**
     * Accepts
     *
     * @return array
     *
     * @access public
     */
    public function accepts()
    {
        return array_keys($this->factories);
    }

    /**
     * Unavailable
     *
     * @return Response
     *
     * @access protected
     */
    protected function notAcceptable()
    {
        $this->response = $this->response->withStatus(406)
            ->withHeader('Content-Type', 'application/json');
        $this->response->getBody()->write(json_encode($this->accepts()));
        return $this->response;
    }

    /**
     * Set
     *
     * @param string  $name     name/type of responder
     * @param calable $callable factory
     *
     * @return $this
     *
     * @access public
     */
    public function set($name, callable $callable)
    {
        $this->factories[$name] = $callable;
        return $this;
    }
    /**
     * Does a named helper exist?
     *
     * @param string $name The responder name.
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->factories[$name]);
    }

    /**
     * Get a responder
     *
     * @param string $name The responder to retrieve.
     *
     * @return object
     */
    public function get($name)
    {
        if (! $this->has($name)) {
            throw new Exception\ResponderNotFoundException(
                'Responder not found for: ' . $name
            );
        }

        $factory = $this->factories[$name];
        return $factory();
    }

    /**
     * Negotiate
     *
     * @param Request $request PSR7 Request
     *
     * @return string | false
     *
     * @access protected
     */
    abstract protected function negotiate(Request $request);
}
