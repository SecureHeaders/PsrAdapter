<?php

namespace SecureHeaders\Psr7HttpAdapter\Tests;

use Aidantwoods\SecureHeaders\HeaderBag;
use SecureHeaders\Psr7HttpAdapter\Adapter;
use PHPUnit_Framework_TestCase;
use Zend\Diactoros\Response;

class AdapterTest extends PHPUnit_Framework_TestCase
{
    public function testProperlyFillsHeaderBag()
    {
        $response = (new Response())
            ->withAddedHeader('Content-Type', 'text/html')
            ->withAddedHeader('X-Foo-Bar', 'val1')
            ->withAddedHeader('X-Foo-Bar', 'val2');

        $adapter = new Adapter($response);
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

        $adapter = new Adapter($response);
        $adapter->sendHeaders(new HeaderBag());

        $finalResponse = $adapter->getFinalResponse();

        $this->assertEquals([], $finalResponse->getHeaders());
    }

    public function testSendsAllHeadersFromHeaderBag()
    {
        $response = (new Response())
            ->withAddedHeader('Content-Type', 'text/html')
            ->withAddedHeader('X-Foo-Bar', 'val1')
            ->withAddedHeader('X-Foo-Bar', 'val2');

        $adapter = new Adapter($response);
        $adapter->sendHeaders(HeaderBag::fromHeaderLines([
            'Content-Type: text/xml',
            'Content-Length: 123',
            'Cache-Control: no-worries :)'
        ]));

        $finalResponse = $adapter->getFinalResponse();

        $this->assertEquals([
            'content-type' => ['text/xml'],
            'content-length' => ['123'],
            'cache-control' => ['no-worries :)']
        ], $finalResponse->getHeaders());
    }
}
