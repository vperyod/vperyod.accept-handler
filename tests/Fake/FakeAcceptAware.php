<?php
// @codingStandardsIgnoreFile

namespace Vperyod\AcceptHandler\Fake;

use Vperyod\AcceptHandler\AcceptRequestAwareTrait;

class FakeAcceptAware
{
    use AcceptRequestAwareTrait;

    public function proxyGetAccept($request)
    {
        return $this->getAccept($request);
    }

    public function proxyNegotiateMedia($request, array $available)
    {
        return $this->negotiateMedia($request, $available);
    }

    public function proxyNegotiateCharset($request, array $available)
    {
        return $this->negotiateCharset($request, $available);
    }

    public function proxyNegotiateLanguage($request, array $available)
    {
        return $this->negotiateLanguage($request, $available);
    }
}
