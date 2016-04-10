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

use Vperyod\AcceptHandler\AcceptRequestAwareTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * NegotiatedMediaResponder
 *
 * @category Responder
 * @package  Vperyod\AcceptHandler
 * @author   Jake Johns <jake@jakejohns.net>
 * @license  http://jnj.mit-license.org/ MIT License
 * @link     https://github.com/vperyod/vperyod.accept-handler
 *
 * @see AbstractResponderLocator
 */
class NegotiatedMediaResponder extends AbstractResponderLocator
{
    use AcceptRequestAwareTrait;

    /**
     * Negotiate
     *
     * @param Request $request PSR7 Request
     *
     * @return string | false
     *
     * @access protected
     */
    protected function negotiate(Request $request)
    {
        $media = $this->negotiateMedia($request, $this->accepts());
        return $media->getValue();
    }
}
