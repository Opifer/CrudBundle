<?php

namespace Opifer\CrudBundle\Tests\Handler;

use Opifer\CrudBundle\Handler\RequestHandler;
use Opifer\CrudBundle\Tests\TestData\User;

class RequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertParams()
    {
        $data = '{"name":"some name","email":"test@email.com"}';
        $expected = ['name' => 'some name', 'email' => 'test@email.com'];

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', ['getContent']);
        $request->expects($this->once())
                ->method('getContent')
                ->will($this->returnValue($data));

        $handler = new RequestHandler();
        $params = $handler->convertParams($request);

        $this->assertEquals($expected, $params);
    }

    public function testHandleRequest()
    {
        $data = '{"name":"some name","email":"test@email.com"}';

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request', ['getContent']);
        $request->expects($this->once())
                ->method('getContent')
                ->will($this->returnValue($data));

        $user = new User();

        $handler = new RequestHandler();
        $user = $handler->handleRequest($request, $user);

        $this->assertEquals('some name', $user->getName());
    }
}
