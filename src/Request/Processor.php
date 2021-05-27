<?php

namespace PoK\Request;

use PoK\Response\Response;
use Psr\Container\ContainerInterface;

abstract class Processor
{
    /**
     * @var ProcessorRequest
     */
    private $request;

    /**
     * @var ContainerInterface|null
     */
    private $container;
    
    public function initialize(ProcessorRequest $request, ContainerInterface $container = null)
    {
        $this->request = $request;
        $this->container = $container;
    }
    
    /**
     * @return ProcessorRequest
     */
    protected function getRequest(): ProcessorRequest
    {
        return $this->request;
    }
    
    /**
     * @return ContainerInterface|null
     */
    protected function getContainer()
    {
        return $this->container;
    }
    
    abstract public function __invoke(): Response;

    /**
     * @param ProcessorRequest $request
     * @param Processor $processor
     * @return Response
     */
    public function reference(ProcessorRequest $request, Processor $processor): Response
    {
        $processor->initialize($request, $this->container);
        return $processor();
    }
}
