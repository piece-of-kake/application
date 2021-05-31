<?php

namespace PoK\Middleware\Interfaces;

use PoK\Request\ProcessorRequest;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ProcessorRequest $processorRequest
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ProcessorRequest $processorRequest);
}

