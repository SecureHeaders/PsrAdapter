<?php

namespace SecureHeaders\PsrHttpAdapter;

use Aidantwoods\SecureHeaders\SecureHeaders;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Secure headers handler
 *
 * Middleware to apply Secure headers to a PSR7 response
 */
class ApplySecureHeaders implements Middleware
{
    /**
     * Headers
     *
     * @var SecureHeaders
     *
     * @access protected
     */
    protected $headers;

    /**
     * __construct
     *
     * @param SecureHeaders $headers Configured headers instance
     *
     * @access public
     */
    public function __construct(SecureHeaders $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Handle PSR7 Request
     *
     * Delegates to middleware chain and applies secure headers to the returned
     * response object before returning it
     *
     * @param Request  $request  Incoming PSR7 request
     * @param Handler  $handler  Server Request Handler
     *
     * @return Response
     *
     * @access public
     */

    public function process(Request $request, Handler $handler): Response
    {
        $response = $handler->handle($request);
        $headers  = $this->headers;
        $adapter  = $this->adapt($response);

        $headers->apply($adapter);
        $response = $adapter->getSecuredResponse();

        return $response;
    }

    /**
     * Adapt a PSR7 Response
     *
     * @param Response $response PSR7 Response
     *
     * @return Psr7Adapter;
     *
     * @access protected
     */
    protected function adapt(Response $response)
    {
        return new Psr7Adapter($response);
    }
}
