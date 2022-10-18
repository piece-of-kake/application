<?php

namespace PoK\Request;

use PoK\Response\Response;

abstract class Processor
{
    /**
     * @var ProcessorRequest
     */
    private $request;

    /**
     * @param ProcessorRequest $request
     * @return ProcessorRequest
     */
    public function initialize(ProcessorRequest $request): Processor
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ProcessorRequest
     */
    protected function getRequest(): ProcessorRequest
    {
        return $this->request;
    }

    abstract public function __invoke(): Response;

    /**
     * @param ProcessorRequest $request
     * @return Response
     */
    public function reference(ProcessorRequest $request, Processor $processor): Response
    {
        $processor->initialize($request);
        return $processor();
    }
}
