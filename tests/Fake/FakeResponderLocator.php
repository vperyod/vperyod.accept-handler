<?php
// @codingStandardsIgnoreFile

namespace Vperyod\AcceptHandler\Fake;

use Vperyod\AcceptHandler\Responder\AbstractResponderLocator;

use Psr\Http\Message\ServerRequestInterface as Request;

class FakeResponderLocator extends AbstractResponderLocator
{
    public $return = 'foo';

    protected function negotiate(Request $request)
    {
        $request;
        return $this->return;
    }
}
