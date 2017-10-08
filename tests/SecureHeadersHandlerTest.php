<?php

namespace SecureHeaders\PsrHttpAdapter\Tests;

use Aidantwoods\SecureHeaders\SecureHeaders;
use PHPUnit_Framework_TestCase;
use SecureHeaders\PsrHttpAdapter;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class SecureHeadersHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testHandler()
    {
        $request  = ServerRequestFactory::fromGlobals();
        $response = new Response();

        $headers = $this
            ->getMockBuilder(SecureHeaders::class)
            ->getMock();

        $headers->expects($this->once())
            ->method('apply')
            ->with($this->isInstanceOf(PsrHttpAdapter\Psr7Adapter::class));

        $handler  = new PsrHttpAdapter\SecureHeadersHandler($headers);

        $test = $this;
        $next = function ($requestIn, $responseIn) use ($test, $request, $response) {
            $test->assertSame($request, $requestIn);
            $test->assertSame($response, $responseIn);
            return $responseIn;
        };

        $output = $handler($request, $response, $next);

        $this->assertInstanceOf(Response::class, $output);
        $this->assertSame($response, $output);
    }

}
