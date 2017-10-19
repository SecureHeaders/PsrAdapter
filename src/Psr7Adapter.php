<?php

namespace SecureHeaders\PsrHttpAdapter;

use Aidantwoods\SecureHeaders\Http\HttpAdapter;
use Aidantwoods\SecureHeaders\HeaderBag;
use Psr\Http\Message\ResponseInterface;

class Psr7Adapter implements HttpAdapter
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Has the response been secured?
     *
     * @var bool
     *
     * @access private
     */
    private $isSecured = false;

    /**
     * @api
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Send the given headers, overwriting all previously send headers
     *
     * @api
     *
     * @param HeaderBag $headers
     * @return void
     */
    public function sendHeaders(HeaderBag $headers)
    {
        # First, remove all headers on the response object
        $headersToRemove = $this->response->getHeaders();
        foreach ($headersToRemove as $name => $headerLines)
        {
            $this->response = $this->response->withoutHeader($name);
        }

        # And then, reset all headers from the HeaderBag instance
        foreach ($headers->get() as $header)
        {
            $this->response = $this->response->withAddedHeader(
                $header->getName(),
                $header->getValue()
            );
        }

        $this->isSecured = true;
    }

    /**
     * Retrieve the current list of already-sent (or planned-to-be-sent) headers
     *
     * @api
     *
     * @return HeaderBag
     */
    public function getHeaders()
    {
        $headerLines = [];
        foreach ($this->response->getHeaders() as $name => $lines)
        {
            foreach ($lines as $line)
            {
                $headerLines[] = "$name: $line";
            }
        }

        return HeaderBag::fromHeaderLines($headerLines);
    }

    /**
     * Retrieve the new PSR-7 response object, with security headers applied
     *
     * @api
     *
     * @return ResponseInterface
     */
    public function getSecuredResponse()
    {
        if (! $this->isSecured) {
            throw new Exception('Response has not been secured');
        }
        return $this->response;
    }
}
