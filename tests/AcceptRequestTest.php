<?php
// @codingStandardsIgnoreFile

namespace Vperyod\AcceptHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class AcceptRequestTest extends \PHPUnit_Framework_TestCase
{
    protected $accept;

    public function setUp()
    {
        $this->accept = $this->getMockBuilder('Aura\Accept\Accept')
            ->disableOriginalConstructor()
            ->getMock();

        $this->req = ServerRequestFactory::fromGlobals()
            ->withAttribute('accept', $this->accept);

        $this->fake = new Fake\FakeAcceptAware;
        $this->fake->setAcceptAttribute('accept');
    }

    public function testGet()
    {
        $this->assertSame(
            $this->accept,
            $this->fake->proxyGetAccept($this->req)
        );
    }

    public function testMedia()
    {
        $avail = ['avail'];

        $this->accept->expects($this->once())
            ->method('negotiateMedia')
            ->with($avail)
            ->will($this->returnValue('foo'));

        $this->assertSame(
            'foo',
            $this->fake->proxyNegotiateMedia($this->req, $avail)
        );
    }

    public function testChar()
    {
        $avail = ['avail'];

        $this->accept->expects($this->once())
            ->method('negotiateCharset')
            ->with($avail)
            ->will($this->returnValue('foo'));

        $this->assertSame(
            'foo',
            $this->fake->proxyNegotiateCharset($this->req, $avail)
        );
    }

    public function testLang()
    {
        $avail = ['avail'];

        $this->accept->expects($this->once())
            ->method('negotiateLanguage')
            ->with($avail)
            ->will($this->returnValue('foo'));

        $this->assertSame(
            'foo',
            $this->fake->proxyNegotiateLanguage($this->req, $avail)
        );
    }

    public function testError()
    {
        $this->setExpectedException('InvalidArgumentException');

        $req = ServerRequestFactory::fromGlobals();

        $fake = new Fake\FakeAcceptAware;

        $fake->proxyGetAccept($req);
    }
}
