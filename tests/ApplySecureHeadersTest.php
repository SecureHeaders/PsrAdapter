<?php

namespace SecureHeaders\PsrHttpAdapter\Tests;

use Aidantwoods\SecureHeaders\SecureHeaders;
use PHPUnit_Framework_TestCase;
use SecureHeaders\PsrHttpAdapter;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;

class ApplySecureHeadersTest extends PHPUnit_Framework_TestCase
{
    public function testHandler()
    {
        $request  = ServerRequestFactory::fromGlobals();
        $headers  = new SecureHeaders;
        $headers->errorReporting(false);
        $handler  = new PsrHttpAdapter\ApplySecureHeaders($headers);

        $next = new class implements Handler {
            public function handle(Request $request) : ResponseInterface
            {
                return new Response();
            }
        };

        $output = $handler->process($request, $next);

        $this->assertInstanceOf('Zend\Diactoros\Response', $output);
    }

}
