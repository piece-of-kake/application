<?php

namespace PoK\Middleware;

use PoK\Middleware\Interfaces\MiddlewareInterface;
use PoK\Middleware\Interfaces\MiddlewareWithRequestInterface;

class ExampleMiddleware implements MiddlewareInterface, MiddlewareWithRequestInterface
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        // implementation
    }
}
