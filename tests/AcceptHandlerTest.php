<?php
// @codingStandardsIgnoreFile

namespace Vperyod\AcceptHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class AcceptHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $accept;

    protected $acceptFactory;

    protected $acceptFactoryFactory;

    protected $media = ['media'];

    public function testHandler()
    {
        $this->accept = $this->getMockBuilder('Aura\Accept\Accept')
            ->disableOriginalConstructor()
            ->getMock();

        $this->acceptFactory = $this->getMockBuilder('Aura\Accept\AcceptFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->acceptFactory->expects($this->once())
            ->method('newInstance')
            ->will($this->returnValue($this->accept));

        $this->acceptFactoryFactory = $this->getMockBuilder('StdClass')
            ->setMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->acceptFactoryFactory->expects($this->once())
            ->method('__invoke')
            ->with($_SERVER, $this->media)
            ->will($this->returnValue($this->acceptFactory));

        $factory = $this->acceptFactoryFactory;

        $this->handler = new AcceptHandler($factory);
        $this->handler->setAcceptAttribute('accept');
        $this->handler->setMediaTypes($this->media);
        $this->handler->__invoke(
            ServerRequestFactory::fromGlobals(),
            new Response(),
            [$this, 'checkRequest']
        );
    }

    public function checkRequest($request, $response)
    {
        $this->assertSame(
            $this->accept,
            $request->getAttribute('accept')
        );

        return $response;
    }

    public function testDefault()
    {
        $test = $this;
        $handler = new AcceptHandler();
        $handler(
            ServerRequestFactory::fromGlobals(),
            new Response(),
            function ($req) use ($test) {
                $test->assertInstanceOf(
                    'Aura\Accept\Accept',
                    $req->getAttribute('aura/accept:accept')
                );
            }
        );
    }
}
