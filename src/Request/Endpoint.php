<?php

namespace PoK\Request;

use PoK\Exception\ClientError\MethodNotAllowedException;
use PoK\Formatter\FormatterInterface;
use PoK\Response\Response;
use PoK\Validator\ValidationManager;
use PoK\Middleware\Interfaces\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Endpoint
{

    private $processorRequest;
    private $httpMethod;
    private $validation;
    private $formatter;
    private $processor;
    private $middleware = [];

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ServerRequestInterface $request, ContainerInterface $container)
    {
        $this->request = $request;

        $parameters = [];
        // Include JSON type body
        if ($request->hasHeader('Content-Type') && $request->getHeaderLine('Content-Type') === 'application/json')
            $parameters += json_decode($request->getBody(), true);
        // Include query parameters (GET)
        if (is_array($this->request->getQueryParams())) $parameters += $this->request->getQueryParams();
        // Include form_data parameters
        if (is_array($this->request->getParsedBody())) $parameters += $this->request->getParsedBody();
        
        $this->processorRequest = new ProcessorRequest($parameters, $this->request->getUploadedFiles());
//        var_dump($this->request->getParsedBody());
        $this->container = $container;
    }

    public function __invoke(): Response
    {
        $this
            ->setHTTPMethod()
            ->validateHTTPMethod()
            ->setMiddleware()
            ->runMiddleware()
            ->setValidation()
            ->runValidation()
            ->setFormatter()
            ->setProcessor();

        return $this->runProcessor();
    }

    protected abstract function httpMethod(): string;

    protected abstract function formatter(): FormatterInterface;

    protected abstract function processor(): Processor;

    protected function isJson(): bool
    {
        return false;
    }

    protected function validate(ValidationManager $validation) {}

    protected function middleware(): array
    {
        return [];
    }

    private function setHTTPMethod(): Endpoint
    {
        $this->httpMethod = $this->httpMethod();
        return $this;
    }

    private function validateHTTPMethod(): Endpoint
    {
        if ($this->httpMethod !== $this->request->getMethod())
            throw new MethodNotAllowedException();
        return $this;
    }

    private function setMiddleware(): Endpoint
    {
        $definedMiddleware = $this->middleware();
        foreach ($definedMiddleware as $middleware) {
            $this->assignMiddleware($middleware);
        }
        return $this;
    }

    private function assignMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    private function runMiddleware(): Endpoint
    {
        foreach ($this->middleware as $middlewareInstance) {
            $middlewareInstance->run($this->request, $this->processorRequest);
        }
        return $this;
    }

    private function setValidation(): Endpoint
    {
        $this->validation = new ValidationManager();
        $this->validate($this->validation);
        return $this;
    }

    private function runValidation(): Endpoint
    {
        $this->validation->validate($this->processorRequest);
        return $this;
    }

    private function setFormatter(): Endpoint
    {
        $this->formatter = $this->formatter();
        return $this;
    }

    private function setProcessor(): Endpoint
    {
        $this->processor = $this->processor();
        return $this;
    }

    private function runProcessor(): Response
    {
        $this->processor->initialize($this->processorRequest, $this->container);
        /**
         * @var Response $response
         */
        $response = $this->processor();
        $response->setResponseDataFormatter($this->formatter);
        if ($this->isJson()) {
            $response->isJson();
        }
        return $response;
    }

}
