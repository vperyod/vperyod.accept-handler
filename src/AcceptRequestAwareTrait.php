<?php
/**
 * Accept Handler
 *
 * PHP version 5
 *
 * Copyright (C) 2016 Jake Johns
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 *
 * @category  Trait
 * @package   Vperyod\AcceptHandler
 * @author    Jake Johns <jake@jakejohns.net>
 * @copyright 2016 Jake Johns
 * @license   http://jnj.mit-license.org/2016 MIT License
 * @link      https://github.com/vperyod/vperyod.accept-handler
 */

namespace Vperyod\AcceptHandler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Aura\Accept\Accept;

/**
 * Accept Request aware trait
 *
 * Trait for objects which need to know where the accept attribute is stored in
 * the request.
 *
 * @category Trait
 * @package  Vperyod\AcceptHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/2016 MIT License
 * @link     https://github.com/vperyod/vperyod.accept-handler
 */
trait AcceptRequestAwareTrait
{
    /**
     * Attribute on request where accept is stored
     *
     * @var string
     *
     * @access protected
     */
    protected $acceptAttribute = 'aura/accept:accept';

    /**
     * Set accept attribute
     *
     * @param string $attr attribute on request where accept is stored
     *
     * @return $this
     *
     * @access public
     */
    public function setAcceptAttribute($attr)
    {
        $this->acceptAttribute = $attr;
        return $this;
    }

    /**
     * Get accept from request
     *
     * @param Request $request PSR7 Request
     *
     * @return Accept
     * @throws InvalidArgumentException if accept attribute is not an `Accept`
     *
     * @access protected
     */
    protected function getAccept(Request $request)
    {
        $accept = $request->getAttribute($this->acceptAttribute);
        if (! $accept instanceof Accept) {
            throw new \InvalidArgumentException(
                'Accept attribute not available in request'
            );
        }
        return $accept;
    }

    /**
     * NegotiateMedia
     *
     * @param Request $request   PSR7 Request
     * @param array   $available array of available media types
     *
     * @return mixed
     *
     * @access protected
     */
    protected function negotiateMedia(Request $request, array $available)
    {
        return $this->getAccept($request)->negotiateMedia($available);
    }

    /**
     * NegotiateCharset
     *
     * @param Request $request   PSR7 Request
     * @param array   $available array of available charsets
     *
     * @return mixed
     *
     * @access protected
     */
    protected function negotiateCharset(Request $request, array $available)
    {
        return $this->getAccept($request)->negotiateCharset($available);
    }

    /**
     * NegotiateLanguage
     *
     * @param Request $request   PSR7 Request
     * @param array   $available arrayof available languages
     *
     * @return mixed
     *
     * @access protected
     */
    protected function negotiateLanguage(Request $request, array $available)
    {
        return $this->getAccept($request)->negotiateLanguage($available);
    }
}
