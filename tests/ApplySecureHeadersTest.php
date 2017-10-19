<?php

namespace SecureHeaders\PsrHttpAdapter\Tests;

use Aidantwoods\SecureHeaders\SecureHeaders;
use PHPUnit_Framework_TestCase;
use SecureHeaders\PsrHttpAdapter;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class ApplySecureHeadersTest extends PHPUnit_Framework_TestCase
{
    public function testHandler()
    {
        $request  = ServerRequestFactory::fromGlobals();
        $response = new Response();
        $headers  = new SecureHeaders;
        $headers->errorReporting(false);
        $handler  = new PsrHttpAdapter\ApplySecureHeaders($headers);

        $next = function ($requestIn, $responseIn) use ($request, $response) {
            $this->assertSame($request, $requestIn);
            $this->assertSame($response, $responseIn);
            return $responseIn;
        };

        $output = $handler($request, $response, $next);

        $this->assertInstanceOf('Zend\Diactoros\Response', $output);
    }

}
