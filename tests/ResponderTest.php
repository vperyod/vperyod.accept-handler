<?php
// @codingStandardsIgnoreFile

namespace Vperyod\AcceptHandler;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class ResponderTest extends \PHPUnit_Framework_TestCase
{
    protected $fooFactory;

    protected $barFactory;

    protected $foobarResponder;

    protected $factories = [];

    protected $request;

    protected $response;

    protected $responder;

    public function setUp()
    {
        parent::setUp();

        $this->fooFactory = $this->getMockBuilder('StdClass')
            ->setMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->barFactory = $this->getMockBuilder('StdClass')
            ->setMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->foobarResponder = $this->getMockBuilder('StdClass')
            ->setMethods(['__invoke'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->factories = [
            'foo' => $this->fooFactory,
            'bar' => $this->barFactory
        ];

        $this->payload = (object) [];

        $this->responder = new Fake\FakeResponderLocator($this->factories);

        $this->request = ServerRequestFactory::fromGlobals();
        $this->response = new Response();
    }

    protected function respond()
    {
        return $this->responder->__invoke(
            $this->request,
            $this->response,
            $this->payload
        );
    }

    protected function initFoo()
    {
        $this->fooFactory->expects($this->once())
            ->method('__invoke')
            ->will($this->returnValue($this->foobarResponder));

        $this->foobarResponder->expects($this->once())
            ->method('__invoke')
            ->with($this->request, $this->response, $this->payload)
            ->will($this->returnValue('response'));
    }

    public function testAcceptable()
    {
        $this->initFoo();
        $this->assertEquals('response', $this->respond());
    }

    public function testNotAvailable()
    {
        $this->setExpectedException(
            'Vperyod\AcceptHandler\Exception\ResponderNotFoundException',
            'Responder not found for: baz'
        );
        $this->responder->get('baz');
    }

    public function testSet()
    {
        $this->initFoo();
        $this->responder = new Fake\FakeResponderLocator([]);
        $this->responder->set('foo', $this->fooFactory);
        $this->assertEquals('response', $this->respond());
    }

    public function testNotAcceptable()
    {
        $this->responder->return = 'bing';
        $response = $this->respond();
        $this->assertInstanceOf('Zend\Diactoros\Response', $response);

        $this->assertEquals(406, $response->getStatusCode());

        $this->assertEquals(
            '["foo","bar"]',
            (string) $response->getBody()
        );
    }

    public function testMediaResponder()
    {
        $this->responder = new Responder\NegotiatedMediaResponder(
            $this->factories
        );

        $media = $this->getMockBuilder('Aura\Accept\Media\MediaValue')
            ->disableOriginalConstructor()
            ->getMock();

        $accept = $this->getMockBuilder('Aura\Accept\Accept')
            ->disableOriginalConstructor()
            ->getMock();

        $accept->expects($this->once())
            ->method('negotiateMedia')
            ->with($this->equalTo(array_keys($this->factories)))
            ->will($this->returnValue($media));

        $media->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('foo'));

        $this->request = $this->request->withAttribute(
            'aura/accept:accept', $accept
        );

        $this->initFoo();

        $this->assertEquals('response', $this->respond());
    }
}
