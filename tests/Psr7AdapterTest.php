<?php

namespace SecureHeaders\PsrHttpAdapter\Tests;

use Aidantwoods\SecureHeaders\HeaderBag;
use SecureHeaders\PsrHttpAdapter\Psr7Adapter;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response;

class Psr7AdapterTest extends TestCase
{

    public function testThrowsException()
    {
        $this->expectException('LogicException');
        $response = new Response;
        $adapter  = new Psr7Adapter($response);
        $adapter->getSecuredResponse();
    }

    public function testProperlyFillsHeaderBag()
    {
        $response = (new Response())
            ->withAddedHeader('Content-Type', 'text/html')
            ->withAddedHeader('X-Foo-Bar', 'val1')
            ->withAddedHeader('X-Foo-Bar', 'val2');

        $adapter = new Psr7Adapter($response);
        $headers = $adapter->getHeaders();

        $this->assertTrue($headers->has('content-type'));
        $this->assertTrue($headers->has('x-foo-bar'));

        $this->assertCount(3, $headers->get());
    }

    public function testRemovesAllPreviousHeadersFromResponse()
    {
        $response = (new Response())
            ->withAddedHeader('Content-Type', 'text/html')
            ->withAddedHeader('X-Foo-Bar', 'val1')
            ->withAddedHeader('X-Foo-Bar', 'val2');

        $adapter = new Psr7Adapter($response);
        $adapter->sendHeaders(new HeaderBag());

        $finalResponse = $adapter->getSecuredResponse();

        $this->assertEquals([], $finalResponse->getHeaders());
    }

    public function testSendsAllHeadersFromHeaderBag()
    {
        $response = (new Response())
            ->withAddedHeader('Content-Type', 'text/html')
            ->withAddedHeader('X-Foo-Bar', 'val1')
            ->withAddedHeader('X-Foo-Bar', 'val2');

        $adapter = new Psr7Adapter($response);
        $adapter->sendHeaders(HeaderBag::fromHeaderLines([
            'Content-Type: text/xml',
            'Content-Length: 123',
            'Cache-Control: no-worries :)'
        ]));

        $finalResponse = $adapter->getSecuredResponse();

        $this->assertEquals([
            'content-type' => ['text/xml'],
            'content-length' => ['123'],
            'cache-control' => ['no-worries :)']
        ], $finalResponse->getHeaders());
    }
}
