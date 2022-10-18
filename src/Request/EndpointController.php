<?php

namespace PoK\Request;

use PoK\ValueObject\TypeString;
use Psr\Http\Message\ServerRequestInterface;

class EndpointController {
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * EndpointController constructor.
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function __invoke(TypeString $domain, TypeString $endpoint)
    {
        $domain = ucfirst($domain);
        $endpoint = ucfirst($endpoint);

        $methodDirectoryName = ucfirst(strtolower($this->request->getMethod()));

        $endpointClassName = "\App\Endpoint\\$domain\\$methodDirectoryName\\$endpoint";

        return $this->executeEndpoint(new $endpointClassName($this->request));
    }

    private function executeEndpoint(Endpoint $endpoint)
    {
        return $endpoint();
    }
}
