<?php

namespace PoK\Request;

use PoK\ValueObject\TypeString;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class EndpointController {
    /**
     * @var ServerRequestInterface
     */
    private $request;
    
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * EndpointController constructor.
     * @param ServerRequestInterface $request
     * @param ContainerInterface|null $container
     */
    public function __construct(ServerRequestInterface $request, ContainerInterface $container = null)
    {
        $this->request = $request;
        $this->container = $container;
    }
    
    public function __invoke(TypeString $domain, TypeString $endpoint)
    {
        $domain = ucfirst($domain);
        $endpoint = ucfirst($endpoint);
        
        $methodDirectoryName = ucfirst(strtolower($this->request->getMethod()));
        
        $endpointClassName = "\App\Endpoint\\$domain\\$methodDirectoryName\\$endpoint";

        // ToDo: throw a MissingEndpointException exception if the class doesn't exist

        $endpointClass = new $endpointClassName($this->request, $this->container);
        
        return $endpointClass();
    }
}
